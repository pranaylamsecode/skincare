<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'service_tag',
        'concern',
        'plan_info',
        'products_used',
        'quote',
        'image_after',
        'image_before',
        'is_featured_result',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_featured_result' => 'boolean',
        'is_active' => 'boolean',
    ];
}
