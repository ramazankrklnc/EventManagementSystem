<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiEventState extends Model
{
    protected $fillable = [
        'event_id',
        'is_published',
        'custom_data'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'custom_data' => 'array'
    ];
} 