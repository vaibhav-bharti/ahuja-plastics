<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionJob extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pending';
    public const STATUS_PARTIAL = 'Partial';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELLED = 'Cancelled';

    protected $fillable = [
        'job_no',
        'production_id',
        'action_id',
        'worker_id',
        'issued_at',
        'issued_weight',
        'returned_weight_total',
        'feed_weight_total',
        'reject_weight_total',
        'good_pcs_total',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'issued_weight' => 'decimal:3',
        'returned_weight_total' => 'decimal:3',
        'feed_weight_total' => 'decimal:3',
        'reject_weight_total' => 'decimal:3',
        'good_pcs_total' => 'integer',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function returns()
    {
        return $this->hasMany(ProductionJobReturn::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
