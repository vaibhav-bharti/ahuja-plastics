<?php

namespace App\Services;

use App\Models\Production;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\StockTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    private const PRECISION = 3;
    private const EPSILON = 0.0005;

    /**
     * Returns the one calculation used by validation, the ledger and the UI.
     * The final line receives the rounding remainder, so line totals always
     * equal the total machine consumption.
     *
     * @return array{prepared: float, consumption: float, allocations: array<int, float>}
     */
    public static function materialConsumption(array|Collection $materials, float $weightPerShot, float $actualCounter): array
    {
        $materials = collect($materials)->values();
        $prepared = round($materials->sum(fn ($row) => (float) data_get($row, 'quantity', 0)), self::PRECISION);
        $consumption = round($weightPerShot * $actualCounter, self::PRECISION);
        $allocations = [];
        $allocated = 0.0;
        $lastIndex = $materials->count() - 1;

        foreach ($materials as $index => $material) {
            $quantity = (float) data_get($material, 'quantity', 0);
            $allocatedQuantity = $index === $lastIndex
                ? round($consumption - $allocated, self::PRECISION)
                : round($prepared > 0 ? $consumption * ($quantity / $prepared) : 0, self::PRECISION);

            $allocations[$index] = (float) max(0, $allocatedQuantity);
            $allocated += $allocations[$index];
        }

        return compact('prepared', 'consumption', 'allocations');
    }

    /** @throws ValidationException */
    public static function validateProductionInput(array $materials, float $weightPerShot, float $actualCounter): array
    {
        $calculation = self::materialConsumption($materials, $weightPerShot, $actualCounter);

        if ($calculation['prepared'] <= 0) {
            throw ValidationException::withMessages(['data.materials' => 'Please add at least one raw material with a quantity greater than 0.']);
        }

        if ($weightPerShot <= 0) {
            throw ValidationException::withMessages(['data.weight_per_shot' => 'Weight per shot must be greater than 0.']);
        }

        if ($actualCounter < 0) {
            throw ValidationException::withMessages(['data.actual_counter' => 'Machine counter cannot be negative.']);
        }

        if ($calculation['consumption'] > $calculation['prepared'] + self::EPSILON) {
            throw ValidationException::withMessages(['data.actual_counter' => sprintf(
                'Actual consumption (%s KG) cannot exceed prepared material (%s KG).',
                number_format($calculation['consumption'], self::PRECISION),
                number_format($calculation['prepared'], self::PRECISION),
            )]);
        }

        return $calculation;
    }

    /**
     * Applies a newly created production. Validation and FIFO deduction run in
     * the same transaction, which closes the validation/consume race window.
     */
    public static function applyProduction(Production $production): void
    {
        DB::transaction(function () use ($production): void {
            $production = Production::query()->lockForUpdate()->findOrFail($production->id);
            $production->load('materials');
            $materials = $production->materials->values();

            $alreadyApplied = StockTransaction::query()
                ->whereIn('reference_type', [Production::class, 'Production'])
                ->where('reference_id', $production->id)
                ->where('transaction_type', 'OUT')
                ->whereNull('reversed_at')
                ->exists();

            if ($alreadyApplied) {
                throw ValidationException::withMessages(['data.materials' => 'This production already has active stock consumption. It cannot be applied twice.']);
            }

            $calculation = self::validateProductionInput(
                $materials->all(),
                (float) $production->weight_per_shot,
                (float) $production->actual_counter,
            );

            self::validateStockAvailability($materials->all(), $calculation['consumption']);

            foreach ($materials as $index => $material) {
                $consumedQuantity = $calculation['allocations'][$index];

                $material->update([
                    'consumed_qty' => $consumedQuantity,
                    'remaining_qty' => round((float) $material->quantity - $consumedQuantity, self::PRECISION),
                ]);

                self::consumeMaterial(
                    (int) $material->raw_material_id,
                    $consumedQuantity,
                    $production,
                    (int) $material->id,
                );
            }

            // Relationships are saved by Filament after the Production model,
            // so the observer cannot reliably calculate this parent total.
            // Persist it here from the same allocation input instead.
            $production->update(['total_material_qty' => $calculation['prepared']]);
        }, 3);
    }

    /** Restores the prior ledger entries before applying a replacement recipe. */
    public static function syncProduction(Production $production, iterable $oldMaterials): void
    {
        DB::transaction(function () use ($production, $oldMaterials): void {
            self::reverseProduction($oldMaterials, $production);
            self::applyProduction($production);
        }, 3);
    }

    /**
     * Reverses each original OUT transaction once. Reversal transactions are
     * audit entries; reversed_at prevents a later edit/delete from restoring
     * the same OUT entry a second time.
     */
    public static function reverseProduction(iterable $oldMaterials, Production $production): void
    {
        DB::transaction(function () use ($oldMaterials, $production): void {
            self::reverseProductionTransactions($oldMaterials, $production);
        }, 3);
    }

    private static function reverseProductionTransactions(iterable $oldMaterials, Production $production): void
    {
        foreach (collect($oldMaterials) as $material) {
            $lineId = (int) data_get($material, 'id');

            if ($lineId <= 0) {
                continue;
            }

            $transactions = StockTransaction::query()
                ->whereIn('reference_type', [Production::class, 'Production'])
                ->where('reference_id', $production->id)
                ->where('reference_line_id', $lineId)
                ->where('transaction_type', 'OUT')
                ->whereNull('reversed_at')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->get();

            foreach ($transactions as $transaction) {
                $stock = RawMaterialStock::query()->lockForUpdate()->find($transaction->raw_material_stock_id);

                if (! $stock) {
                    throw ValidationException::withMessages(['data.materials' => 'A stock batch used by this production no longer exists and cannot be restored.']);
                }

                $quantity = (float) $transaction->quantity;
                $stock->increment('available_qty', $quantity);
                $stock->refresh();

                StockTransaction::create([
                    'raw_material_stock_id' => $stock->id,
                    'transaction_type' => 'ADJUSTMENT',
                    'quantity' => $quantity,
                    'balance_qty' => $stock->available_qty,
                    'reference_type' => Production::class,
                    'reference_id' => $production->id,
                    'reference_line_id' => $lineId,
                    'remarks' => 'Production consumption reversed',
                    'created_by' => auth()->id(),
                ]);

                $transaction->update(['reversed_at' => now()]);
            }
        }
    }

    /**
     * Checks aggregate demand per material. It locks the exact FIFO rows when
     * called from applyProduction, so a passing check cannot be invalidated by
     * another writer before consumeMaterial runs.
     *
     * @throws ValidationException
     */
    public static function validateStockAvailability(array $materials, float $actualConsumption, ?iterable $oldMaterials = null): void
    {
        $prepared = round(collect($materials)->sum(fn ($row) => (float) data_get($row, 'quantity', 0)), self::PRECISION);
        if ($prepared <= 0) {
            return;
        }

        $demands = [];
        $lineKeysByMaterial = [];
        $materialKeys = array_keys($materials);
        foreach (self::materialConsumption($materials, 1, $actualConsumption)['allocations'] as $index => $quantity) {
            $materialKey = $materialKeys[$index];
            $rawMaterialId = (int) data_get($materials[$materialKey] ?? null, 'raw_material_id');
            if ($rawMaterialId > 0) {
                $demands[$rawMaterialId] = round(($demands[$rawMaterialId] ?? 0) + $quantity, self::PRECISION);
                $lineKeysByMaterial[$rawMaterialId][] = $materialKey;
            }
        }

        // Only credit consumption which can actually be restored into an active
        // stock batch. This exactly mirrors reverseProduction() and avoids an
        // edit passing when an old batch was disabled after the first save.
        $oldByLine = collect($oldMaterials ?? [])->keyBy(fn ($row) => (int) data_get($row, 'id'));
        $credits = collect();
        if ($oldByLine->isNotEmpty()) {
            $restorableByLine = StockTransaction::query()
                ->join('raw_material_stocks', 'raw_material_stocks.id', '=', 'stock_transactions.raw_material_stock_id')
                ->whereIn('stock_transactions.reference_line_id', $oldByLine->keys())
                ->whereIn('stock_transactions.reference_type', [Production::class, 'Production'])
                ->where('stock_transactions.transaction_type', 'OUT')
                ->whereNull('stock_transactions.reversed_at')
                ->where('raw_material_stocks.status', true)
                ->groupBy('stock_transactions.reference_line_id')
                ->pluck(DB::raw('SUM(stock_transactions.quantity)'), 'stock_transactions.reference_line_id');

            foreach ($restorableByLine as $lineId => $quantity) {
                $rawMaterialId = (int) data_get($oldByLine->get($lineId), 'raw_material_id');
                $credits[$rawMaterialId] = (float) ($credits[$rawMaterialId] ?? 0) + (float) $quantity;
            }
        }

        $errors = [];
        ksort($demands);
        foreach ($demands as $rawMaterialId => $required) {
            $stocks = RawMaterialStock::query()
                ->where('raw_material_id', $rawMaterialId)
                ->where('status', true)
                ->where('available_qty', '>', 0)
                ->orderBy('purchase_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $available = round((float) $stocks->sum('available_qty') + (float) ($credits[$rawMaterialId] ?? 0), self::PRECISION);
            if ($required > $available + self::EPSILON) {
                $name = RawMaterial::find($rawMaterialId)?->name ?? 'Material';
                $message = sprintf(
                    '%s stock is insufficient. Required: %s KG, available: %s KG, shortage: %s KG.',
                    $name,
                    number_format($required, 3),
                    number_format($available, 3),
                    number_format($required - $available, 3),
                );

                // Attach the message to the exact Repeater material field so
                // Filament displays it beside the offending selection.
                foreach ($lineKeysByMaterial[$rawMaterialId] as $materialKey) {
                    $errors["data.materials.{$materialKey}.raw_material_id"] = $message;
                }
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private static function consumeMaterial(int $rawMaterialId, float $quantity, Production $production, int $lineId): void
    {
        $remaining = $quantity;
        $stocks = RawMaterialStock::query()
            ->where('raw_material_id', $rawMaterialId)
            ->where('status', true)
            ->where('available_qty', '>', 0)
            ->orderBy('purchase_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($stocks as $stock) {
            if ($remaining <= self::EPSILON) {
                break;
            }

            $consumed = min((float) $stock->available_qty, $remaining);
            $stock->decrement('available_qty', $consumed);
            $stock->refresh();

            StockTransaction::create([
                'raw_material_stock_id' => $stock->id,
                'transaction_type' => 'OUT',
                'quantity' => $consumed,
                'balance_qty' => $stock->available_qty,
                'reference_type' => Production::class,
                'reference_id' => $production->id,
                'reference_line_id' => $lineId,
                'remarks' => 'Production consumption',
                'created_by' => auth()->id(),
            ]);
            $remaining = round($remaining - $consumed, self::PRECISION);
        }

        if ($remaining > self::EPSILON) {
            // This is a protected fallback: normally the locked pre-check above
            // makes it unreachable, but it guarantees a user-visible rollback.
            throw ValidationException::withMessages(['data.materials' => 'Stock changed while production was being saved. Please review availability and try again.']);
        }
    }
}
