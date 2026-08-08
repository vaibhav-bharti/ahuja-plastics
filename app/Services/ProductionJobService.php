<?php

namespace App\Services;

use App\Models\Action;
use App\Models\ProductActionRate;
use App\Models\Production;
use App\Models\ProductionJob;
use App\Models\ProductionJobReturn;
use App\Models\Worker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionJobService
{
    public function createJob(array $data): ProductionJob
    {
        return DB::transaction(function () use ($data): ProductionJob {
            $data = $this->validateJob($data);

            return ProductionJob::create([
                ...Arr::only($data, [
                    'production_id', 'action_id', 'worker_id', 'issued_at', 'issued_weight', 'remarks', 'created_by',
                ]),
                'job_no' => $this->generateJobNumber(),
                'status' => ProductionJob::STATUS_PENDING,
            ]);
        }, 3);
    }

    public function updateJob(ProductionJob $job, array $data): ProductionJob
    {
        return DB::transaction(function () use ($job, $data): ProductionJob {
            $job = ProductionJob::query()->lockForUpdate()->findOrFail($job->id);
            $data = $this->validateJob($data, $job->id);

            $accountedWeight = $job->accountedWeight();
            if ((float) $data['issued_weight'] + 0.0005 < $accountedWeight) {
                throw ValidationException::withMessages([
                    'data.issued_weight' => sprintf(
                        'Issued weight cannot be less than the already recorded %s KG.',
                        number_format($accountedWeight, 3),
                    ),
                ]);
            }

            $job->update(Arr::only($data, [
                'production_id', 'action_id', 'worker_id', 'issued_at', 'issued_weight', 'remarks',
            ]));

            return $this->recalculateTotals($job);
        }, 3);
    }

    public function addReturn(ProductionJob $job, array $data): ProductionJobReturn
    {
        return DB::transaction(function () use ($job, $data): ProductionJobReturn {
            $job = ProductionJob::query()->lockForUpdate()->findOrFail($job->id);

            if ($job->status === ProductionJob::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'data.production_job_id' => 'Returns cannot be added to a cancelled job.',
                ]);
            }

            $rate = $this->rateForJob($job);

            if ($rate === null) {
                throw ValidationException::withMessages([
                    'data.production_job_id' => 'No active product action rate is configured for this job.',
                ]);
            }

            $returnWeight = $this->weight($data['return_weight'] ?? 0);
            $feedWeight = $this->weight($data['feed_weight'] ?? 0);
            $rejectWeight = $this->weight($data['reject_weight'] ?? 0);
            $this->validateReturnQuantities($job, $returnWeight, $feedWeight, $rejectWeight);

            $return = $job->returns()->create([
                ...Arr::only($data, [
                    'return_date', 'feed_weight', 'reject_weight', 'remarks', 'created_by',
                ]),
                'return_weight' => $returnWeight,
                'feed_weight' => $feedWeight,
                'reject_weight' => $rejectWeight,
                'good_pcs' => $this->goodPieces($job, $returnWeight),
                'rate' => $rate,
                'amount' => $this->amount($returnWeight, $rate),
            ]);

            $this->recalculateTotals($job);

            return $return->fresh();
        }, 3);
    }

    public function updateReturn(ProductionJobReturn $return, array $data): ProductionJobReturn
    {
        return DB::transaction(function () use ($return, $data): ProductionJobReturn {
            $return = ProductionJobReturn::query()->lockForUpdate()->findOrFail($return->id);
            $job = ProductionJob::query()->lockForUpdate()->findOrFail($return->production_job_id);

            if ($job->status === ProductionJob::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'data.production_job_id' => 'Returns cannot be changed for a cancelled job.',
                ]);
            }

            $returnWeight = $this->weight($data['return_weight'] ?? $return->return_weight);
            $feedWeight = $this->weight($data['feed_weight'] ?? $return->feed_weight);
            $rejectWeight = $this->weight($data['reject_weight'] ?? $return->reject_weight);
            $this->validateReturnQuantities($job, $returnWeight, $feedWeight, $rejectWeight, $return->id);

            $return->update([
                ...Arr::only($data, ['return_date', 'feed_weight', 'reject_weight', 'good_pcs', 'remarks']),
                'return_weight' => $returnWeight,
                'feed_weight' => $feedWeight,
                'reject_weight' => $rejectWeight,
                'good_pcs' => $this->goodPieces($job, $returnWeight),
                // The rate is intentionally retained as the original snapshot.
                'amount' => $this->amount($returnWeight, $return->rate),
            ]);

            $this->recalculateTotals($job);

            return $return->fresh();
        }, 3);
    }

    public function deleteReturn(ProductionJobReturn $return): void
    {
        DB::transaction(function () use ($return): void {
            $return = ProductionJobReturn::query()->lockForUpdate()->findOrFail($return->id);
            $job = ProductionJob::query()->lockForUpdate()->findOrFail($return->production_job_id);

            $return->delete();
            $this->recalculateTotals($job);
        }, 3);
    }

    /**
     * Read-only summary for the form. Only active jobs reduce production
     * allocation; cancelled jobs are deliberately excluded.
     *
     * @return array{output: float, assigned: float, remaining: float}
     */
    public function allocationSummary(int $productionId, ?int $exceptJobId = null): array
    {
        $production = Production::query()->with('product:id,pcs_per_kg')->find($productionId);
        if (! $production) {
            return ['output' => 0.0, 'assigned' => 0.0, 'remaining' => 0.0];
        }

        $output = $this->productionOutputWeight($production);
        $assignedQuery = ProductionJob::query()
            ->where('production_id', $production->id)
            ->where('status', '!=', ProductionJob::STATUS_CANCELLED);

        if ($exceptJobId) {
            $assignedQuery->whereKeyNot($exceptJobId);
        }

        $assigned = round((float) $assignedQuery->sum('issued_weight'), 3);

        return [
            'output' => $output,
            'assigned' => $assigned,
            'remaining' => round(max(0, $output - $assigned), 3),
        ];
    }

    /** @return array{accounted_remaining: float} */
    public function returnSummary(int $jobId, ?int $exceptReturnId = null): array
    {
        $job = ProductionJob::query()->find($jobId);
        if (! $job) {
            return ['accounted_remaining' => 0.0];
        }

        $query = $job->returns();
        if ($exceptReturnId) {
            $query->whereKeyNot($exceptReturnId);
        }

        $totals = $query
            ->selectRaw('COALESCE(SUM(return_weight + feed_weight + reject_weight), 0) as accounted_weight')
            ->first();

        return [
            'accounted_remaining' => round(max(0, (float) $job->issued_weight - (float) $totals->accounted_weight), 3),
        ];
    }

    public function recalculateTotals(ProductionJob $job): ProductionJob
    {
        $totals = $job->returns()
            ->selectRaw('COALESCE(SUM(return_weight), 0) as returned_weight_total')
            ->selectRaw('COALESCE(SUM(feed_weight), 0) as feed_weight_total')
            ->selectRaw('COALESCE(SUM(reject_weight), 0) as reject_weight_total')
            ->selectRaw('COALESCE(SUM(good_pcs), 0) as good_pcs_total')
            ->first();

        $job->forceFill([
            'returned_weight_total' => $totals->returned_weight_total,
            'feed_weight_total' => $totals->feed_weight_total,
            'reject_weight_total' => $totals->reject_weight_total,
            'good_pcs_total' => $totals->good_pcs_total,
        ]);

        $this->updateStatus($job);
        $job->save();

        return $job->fresh();
    }

    /** Returns the currently configured product/action rate for a new return. */
    public function rateForJob(ProductionJob $job): ?string
    {
        $productId = $job->production()->value('product_id');

        if (! $productId) {
            return null;
        }

        $rate = ProductActionRate::query()
            ->where('product_id', $productId)
            ->where('action_id', $job->action_id)
            ->where('status', true)
            ->value('rate');

        return $rate === null ? null : number_format((float) $rate, 2, '.', '');
    }

    /** Returns the active product/action rate used by the Job form preview. */
    public function rateForProductionAction(int $productionId, int $actionId): ?string
    {
        $production = Production::query()->find($productionId);
        if (! $production) {
            return null;
        }

        $rate = ProductActionRate::query()
            ->where('product_id', $production->product_id)
            ->where('action_id', $actionId)
            ->where('status', true)
            ->value('rate');

        return $rate === null ? null : number_format((float) $rate, 2, '.', '');
    }

    public function updateStatus(ProductionJob $job): ProductionJob
    {
        $accountedWeight = $job->accountedWeight();
        $issuedWeight = (float) $job->issued_weight;

        $job->status = match (true) {
            $accountedWeight <= 0 => ProductionJob::STATUS_PENDING,
            $accountedWeight < $issuedWeight => ProductionJob::STATUS_PARTIAL,
            default => ProductionJob::STATUS_COMPLETED,
        };

        return $job;
    }

    private function generateJobNumber(): string
    {
        // Short sequential references are easy for workers to communicate.
        // The surrounding transaction and unique database index protect this
        // sequence; the ID is also used for a stable legacy display reference.
        $lastId = ProductionJob::query()
            ->lockForUpdate()
            ->max('id');

        return sprintf('PJ-%05d', ((int) $lastId) + 1);
    }

    /**
     * Performs the authoritative, locked allocation validation used for both
     * UI submission and concurrent supervisor requests.
     *
     * @return array<string, mixed>
     */
    private function validateJob(array $data, ?int $exceptJobId = null): array
    {
        $productionId = (int) ($data['production_id'] ?? 0);
        $actionId = (int) ($data['action_id'] ?? 0);
        $workerId = (int) ($data['worker_id'] ?? 0);
        $issuedWeight = $this->weight($data['issued_weight'] ?? 0);

        $production = Production::query()
            ->with('product:id,pcs_per_kg')
            ->lockForUpdate()
            ->find($productionId);

        if (! $production) {
            throw ValidationException::withMessages(['data.production_id' => 'Please select a valid production.']);
        }

        if ((float) $issuedWeight <= 0) {
            throw ValidationException::withMessages(['data.issued_weight' => 'Issued weight must be greater than 0.']);
        }

        if (! Worker::query()->whereKey($workerId)->where('status', true)->exists()) {
            throw ValidationException::withMessages(['data.worker_id' => 'Please select an active worker.']);
        }

        if (! Action::query()->whereKey($actionId)->where('status', true)->exists()) {
            throw ValidationException::withMessages(['data.action_id' => 'Please select an active action.']);
        }

        if ($this->rateForProductionAction($production->id, $actionId) === null) {
            throw ValidationException::withMessages(['data.action_id' => 'No active rate is configured for this production product and action.']);
        }

        $output = $this->productionOutputWeight($production);
        if ($output <= 0) {
            throw ValidationException::withMessages(['data.production_id' => 'This production has no allocatable output weight.']);
        }

        $assignedQuery = ProductionJob::query()
            ->where('production_id', $production->id)
            ->where('status', '!=', ProductionJob::STATUS_CANCELLED)
            ->lockForUpdate();

        if ($exceptJobId) {
            $assignedQuery->whereKeyNot($exceptJobId);
        }

        $assignedWeight = (float) $assignedQuery->get(['issued_weight'])->sum('issued_weight');
        $remaining = round(max(0, $output - $assignedWeight), 3);
        if ((float) $issuedWeight > $remaining + 0.0005) {
            throw ValidationException::withMessages([
                'data.issued_weight' => sprintf('Only %s KG is available for this production.', number_format($remaining, 3)),
            ]);
        }

        return $data;
    }

    private function validateReturnQuantities(
        ProductionJob $job,
        string $returnWeight,
        string $feedWeight,
        string $rejectWeight,
        ?int $exceptReturnId = null,
    ): void
    {
        $returnedQuery = $job->returns();
        if ($exceptReturnId) {
            $returnedQuery->whereKeyNot($exceptReturnId);
        }

        $previouslyAccounted = (float) $returnedQuery
            ->lockForUpdate()
            ->get(['return_weight', 'feed_weight', 'reject_weight'])
            ->sum(fn (ProductionJobReturn $item): float => (float) $item->return_weight + (float) $item->feed_weight + (float) $item->reject_weight);
        $remaining = round(max(0, (float) $job->issued_weight - $previouslyAccounted), 3);
        $enteredTotal = round((float) $returnWeight + (float) $feedWeight + (float) $rejectWeight, 3);

        if ($enteredTotal > $remaining + 0.0005) {
            throw ValidationException::withMessages([
                'data.return_weight' => sprintf('Return + Feed + Reject cannot exceed the available %s KG for this job.', number_format($remaining, 3)),
                'data.feed_weight' => 'Reduce the combined Return, Feed, and Reject weight.',
                'data.reject_weight' => 'Reduce the combined Return, Feed, and Reject weight.',
            ]);
        }
    }

    private function productionOutputWeight(Production $production): float
    {
        $pcsPerKg = (float) ($production->product?->pcs_per_kg ?? 0);

        return $pcsPerKg > 0
            ? round((float) $production->actual_production / $pcsPerKg, 3)
            : 0.0;
    }

    private function weight(mixed $value): string
    {
        return number_format(max(0, (float) $value), 3, '.', '');
    }

    private function amount(string $weight, mixed $rate): string
    {
        return number_format((float) $weight * (float) $rate, 2, '.', '');
    }

    private function goodPieces(ProductionJob $job, string $returnWeight): int
    {
        $pcsPerKg = (float) $job->production()->with('product')->first()?->product?->pcs_per_kg;

        return (int) round((float) $returnWeight * $pcsPerKg);
    }
}
