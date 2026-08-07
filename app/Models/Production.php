<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [

        'production_date',

        'shift_id',

        'machine_id',

        'operator_id',

        'product_id',

        /*
        |--------------------------------------------------------------------------
        | Snapshot
        |--------------------------------------------------------------------------
        */

        'cavity',

        'cycle_time',

        'pcs_per_kg',

        'weight_per_shot',

        'shift_start',

        'shift_end',

        /*
        |--------------------------------------------------------------------------
        | Calculated
        |--------------------------------------------------------------------------
        */

        'planned_quantity',

        'predicted_counter',

        'actual_counter',

        'actual_production',

        'production_difference',

        /*
        |--------------------------------------------------------------------------
        | Other
        |--------------------------------------------------------------------------
        */

        'remarks',

        'status',

        'created_by',

    ];

    protected $casts = [

        'production_date' => 'date',

        'status' => 'boolean',

        'pcs_per_kg' => 'decimal:2',

        'weight_per_shot' => 'decimal:3',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterial::class);
    }

    public function downtimes()
    {
        return $this->hasMany(ProductionDowntime::class);
    }

    public function jobs()
    {
        return $this->hasMany(ProductionJob::class);
    }
}
