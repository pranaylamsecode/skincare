<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'name',
        'phone',
        'email',
        'pincode',
        'service_id',
        'service_name',
        'specialist_id',
        'specialist_name',
        'booking_date',
        'time_slot_id',
        'time_slot_text',
        'skin_concern',
        'status',
        'total_amount',
        'payment_status',
        'payment_method',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'admin_notes',
        'consent',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'consent' => 'boolean',
        'total_amount' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
