<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mould extends Model
{
    use HasFactory;

    protected $fillable = [
        'mould_no',
        'name',
        'brand',
        'cavity',
        'cycle_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}