<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RawMaterialStock extends Model
{
    use HasFactory;

    protected $fillable = [

        'raw_material_id',

        'purchase_date',

        'vendor_name',

        'invoice_no',

        'purchase_qty',

        'available_qty',

        'purchase_price',

        'total_amount',

        'remarks',

        'status',

        'created_by',

    ];

    protected function casts(): array
    {
        return [

            'purchase_date' => 'date',

            'purchase_qty' => 'decimal:3',

            'available_qty' => 'decimal:3',

            'purchase_price' => 'decimal:2',

            'total_amount' => 'decimal:2',

            'status' => 'boolean',

        ];
    }

    public function material()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function productionMaterials()
    {
        return $this->hasMany(
            ProductionMaterial::class,
            'raw_material_stock_id'
        );
    }
}