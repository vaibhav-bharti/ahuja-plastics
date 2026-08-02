<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionDowntime extends Model
{
    use HasFactory;

    protected $fillable = [

        'production_id',

        'start_time',

        'end_time',

        'total_minutes',

        'reason',

        'remarks',

    ];

    protected $casts = [

        'total_minutes' => 'integer',

    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }
}