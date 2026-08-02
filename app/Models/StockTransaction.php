<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [

        'raw_material_stock_id',

        'transaction_type',

        'quantity',

        'balance_qty',

        'reference_type',

        'reference_id',

        'remarks',

        'created_by',

    ];

    protected function casts(): array
    {
        return [

            'quantity' => 'decimal:3',

            'balance_qty' => 'decimal:3',

        ];
    }

    public function stock()
    {
        return $this->belongsTo(
            RawMaterialStock::class,
            'raw_material_stock_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}