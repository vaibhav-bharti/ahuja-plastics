<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'remarks',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function productRates()
    {
        return $this->hasMany(ProductActionRate::class);
    }
    public function productActionRates()
    {
        return $this->hasMany(ProductActionRate::class);
    }
}