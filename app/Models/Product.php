<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mould_id',
        'status',
        'remarks',
        'pcs_per_kg',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function mould()
    {
        return $this->belongsTo(Mould::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
    public function actionRates()
    {
        return $this->hasMany(ProductActionRate::class);
    }
}