<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterial extends Model
{
    use HasFactory;

    protected $fillable = [

        'production_id',
        'raw_material_id',
        'quantity',
        'consumed_qty',      // System Calculated
        'remaining_qty', 
        'remarks',

    ];

    protected $casts = [

        'quantity' => 'decimal:3',
        'consumed_qty' => 'decimal:3',
        'remaining_qty' => 'decimal:3',

    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}