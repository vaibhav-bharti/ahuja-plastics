<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductActionRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'action_id',
        'rate',
        'status',
        'remarks',
        'sort_order',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}