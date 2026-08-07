<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionJobReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_job_id',
        'return_date',
        'return_weight',
        'feed_weight',
        'reject_weight',
        'good_pcs',
        'rate',
        'amount',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'return_weight' => 'decimal:3',
        'feed_weight' => 'decimal:3',
        'reject_weight' => 'decimal:3',
        'good_pcs' => 'integer',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function productionJob()
    {
        return $this->belongsTo(ProductionJob::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
