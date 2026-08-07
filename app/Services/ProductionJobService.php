<?php

namespace App\Services;

use App\Models\ProductActionRate;
use App\Models\ProductionJob;
use App\Models\ProductionJobReturn;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductionJobService
{
    public function createJob(array $data): ProductionJob
    {
        return DB::transaction(function () use ($data): ProductionJob {
            return ProductionJob::create([
                ...Arr::only($data, [
                    'production_id', 'action_id', 'worker_id', 'issued_at', 'issued_weight', 'remarks', 'created_by',
                ]),
                'job_no' => $this->generateJobNumber(),
                'status' => ProductionJob::STATUS_PENDING,
            ]);
        });
    }

    public function addReturn(ProductionJob $job, array $data): ProductionJobReturn
    {
        return DB::transaction(function () use ($job, $data): ProductionJobReturn {
            $job = ProductionJob::query()->lockForUpdate()->findOrFail($job->id);

            if ($job->status === ProductionJob::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'production_job_id' => 'Returns cannot be added to a cancelled job.',
                ]);
            }

            $rate = ProductActionRate::query()
                ->where('product_id', $job->production()->value('product_id'))
                ->where('action_id', $job->action_id)
                ->where('status', true)
                ->value('rate');

            if ($rate === null) {
                throw ValidationException::withMessages([
                    'production_job_id' => 'No active product action rate is configured for this job.',
                ]);
            }

            $returnWeight = $this->weight($data['return_weight'] ?? 0);

            $return = $job->returns()->create([
                ...Arr::only($data, [
                    'return_date', 'feed_weight', 'reject_weight', 'good_pcs', 'remarks', 'created_by',
                ]),
                'return_weight' => $returnWeight,
                'rate' => $rate,
                'amount' => $this->amount($returnWeight, $rate),
            ]);

            $this->recalculateTotals($job);

            return $return->fresh();
        });
    }

    public function updateReturn(ProductionJobReturn $return, array $data): ProductionJobReturn
    {
        return DB::transaction(function () use ($return, $data): ProductionJobReturn {
            $return = ProductionJobReturn::query()->lockForUpdate()->findOrFail($return->id);
            $job = ProductionJob::query()->lockForUpdate()->findOrFail($return->production_job_id);
            $returnWeight = $this->weight($data['return_weight'] ?? $return->return_weight);

            $return->update([
                ...Arr::only($data, ['return_date', 'feed_weight', 'reject_weight', 'good_pcs', 'remarks']),
                'return_weight' => $returnWeight,
                // The rate is intentionally retained as the original snapshot.
                'amount' => $this->amount($returnWeight, $return->rate),
            ]);

            $this->recalculateTotals($job);

            return $return->fresh();
        });
    }

    public function deleteReturn(ProductionJobReturn $return): void
    {
        DB::transaction(function () use ($return): void {
            $return = ProductionJobReturn::query()->lockForUpdate()->findOrFail($return->id);
            $job = ProductionJob::query()->lockForUpdate()->findOrFail($return->production_job_id);

            $return->delete();
            $this->recalculateTotals($job);
        });
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

    public function updateStatus(ProductionJob $job): ProductionJob
    {
        $returnedWeight = (float) $job->returned_weight_total;
        $issuedWeight = (float) $job->issued_weight;

        $job->status = match (true) {
            $returnedWeight <= 0 => ProductionJob::STATUS_PENDING,
            $returnedWeight < $issuedWeight => ProductionJob::STATUS_PARTIAL,
            default => ProductionJob::STATUS_COMPLETED,
        };

        return $job;
    }

    private function generateJobNumber(): string
    {
        return 'PJ-'.now()->format('Ymd').'-'.Str::upper((string) Str::ulid());
    }

    private function weight(mixed $value): string
    {
        return number_format(max(0, (float) $value), 3, '.', '');
    }

    private function amount(string $weight, mixed $rate): string
    {
        return number_format((float) $weight * (float) $rate, 2, '.', '');
    }
}
