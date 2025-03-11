<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreHour extends Model
{
    /** @use HasFactory<\Database\Factories\StoreHourFactory> */
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_open',
        'is_alternate_saturday',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];
}
