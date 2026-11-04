<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concern extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote',
        'solution',
        'link_text',
        'link_target',
        'is_dark',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_dark' => 'boolean',
        'is_active' => 'boolean',
    ];
}
