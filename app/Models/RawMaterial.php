<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RawMaterialStock;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
        'remarks',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function productionMaterials()
    {
        return $this->hasMany(ProductionMaterial::class);
    }
    public function stocks()
    {
        return $this->hasMany(RawMaterialStock::class);
    }
}