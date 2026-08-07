<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_code',
        'name',
        'phone',
        'status',
        'remarks',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Worker $worker): void {
            if (blank($worker->worker_code)) {
                $worker->worker_code = 'WRK'.str_pad(
                    (string) ((static::max('id') ?? 0) + 1),
                    5,
                    '0',
                    STR_PAD_LEFT,
                );
            }
        });
    }

    public function productionJobs()
    {
        return $this->hasMany(ProductionJob::class);
    }
}
