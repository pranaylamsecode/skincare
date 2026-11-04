<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialist_id',
        'period',
        'start_time',
        'end_time',
        'day_of_week',
        'specific_date',
        'max_bookings',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'specific_date' => 'date',
    ];

    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getDisplayTextAttribute()
    {
        $spec = $this->specialist ? " - {$this->specialist->name}" : "";
        return "{$this->period} ({$this->start_time} - {$this->end_time}){$spec}";
    }
}
