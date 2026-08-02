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

        'remarks',

    ];

    protected $casts = [

        'quantity' => 'decimal:3',

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