<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'duration_days',
        'base_price_bdt',
        'hero_image_url',
        'is_active',
        'is_featured_ongoing',
        'next_start_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured_ongoing' => 'boolean',
        'next_start_date' => 'date',
    ];
}
