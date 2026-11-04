<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'qualification',
        'location',
        'pincode',
        'city',
        'languages',
        'badge',
        'photo',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'languages' => 'array',
        'is_active' => 'boolean',
    ];

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
