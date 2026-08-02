<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'machine_no',
        'name',
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
}