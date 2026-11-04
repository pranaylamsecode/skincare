<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Plan;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\TimeSlot;
use App\Services\EmailNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Get available slots for a chosen date and optional specialist
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'specialist_id' => 'nullable|integer',
        ]);

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $specialistId = $request->specialist_id;

        // Query slots: either matching specialist, or general (null specialist)
        $query = TimeSlot::where('is_active', true);

        if ($specialistId) {
            $query->where(function ($q) use ($specialistId) {
                $q->where('specialist_id', $specialistId)
                  ->orWhereNull('specialist_id');
            });
        }

        $allSlots = $query->orderBy('period')->orderBy('start_time')->get();

        // Check booked count for each slot on this date
        $availableSlots = [];
        foreach ($allSlots as $slot) {
            $bookedCount = Booking::where('booking_date', $date)
                ->where('time_slot_id', $slot->id)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->count();

            $isAvailable = $bookedCount < $slot->max_bookings;

            $availableSlots[] = [
                'id' => $slot->id,
                'period' => $slot->period,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'display' => "{$slot->period} ({$slot->start_time} - {$slot->end_time})",
                'specialist_id' => $slot->specialist_id,
                'is_available' => $isAvailable,
                'remaining' => max(0, $slot->max_bookings - $bookedCount),
            ];
        }

        return response()->json([
            'success' => true,
            'date' => $date,
            'slots' => $availableSlots,
        ]);
    }

    /**
     * Store new customer booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'regex:/^[0-9+\s()\-]{8,18}$/'],
            'email' => 'nullable|email|max:150',
            'pincode' => 'required|string|size:6',
            'service_name' => 'required|string|max:150',
            'service_id' => 'nullable|integer',
            'specialist_id' => 'nullable|integer',
            'specialist_name' => 'nullable|string|max:100',
            'date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'nullable|integer',
            'time_slot_text' => 'nullable|string|max:100',
            'msg' => 'nullable|string|max:2000',
            'consent' => 'required|accepted',
            'payment_method' => 'nullable|string|in:after_visit,razorpay',
        ]);

        // Smart price and service resolution
        $service = null;
        $totalAmount = 0;

        if (!empty($validated['service_id'])) {
            $service = Service::find($validated['service_id']);
        }

        if (!$service && !empty($validated['service_name'])) {
            $service = Service::where('title', $validated['service_name'])
                ->orWhere('title', 'like', "%{$validated['service_name']}%")
                ->first();

            if (!$service) {
                if (stripos($validated['service_name'], 'Cleanse') !== false) {
                    $service = Service::where('title', 'like', '%Cleanse%')->first();
                } elseif (stripos($validated['service_name'], 'Facial') !== false || stripos($validated['service_name'], 'LumiSpa') !== false || stripos($validated['service_name'], 'Galvanic') !== false) {
                    $service = Service::where('title', 'like', '%Facial%')->first();
                } elseif (stripos($validated['service_name'], 'Ritual') !== false || stripos($validated['service_name'], 'Radiance') !== false) {
                    $service = Service::where('title', 'like', '%Ritual%')->first();
                }
            }
        }

        if ($service) {
            $totalAmount = $service->price;
        } elseif (stripos($validated['service_name'], 'Plan') !== false || stripos($validated['service_name'], '3-Visit') !== false) {
            $plan = Plan::where('title', 'like', '%3-Visit%')->first();
            $totalAmount = $plan ? $plan->price : 3299;
        }

        // Find specialist
        $specialist = null;
        if (!empty($validated['specialist_id'])) {
            $specialist = Specialist::find($validated['specialist_id']);
        }

        // Find slot
        $timeSlot = null;
        $slotText = $validated['time_slot_text'] ?? '';
        if (!empty($validated['time_slot_id'])) {
            $timeSlot = TimeSlot::find($validated['time_slot_id']);
            if ($timeSlot) {
                $slotText = "{$timeSlot->period} ({$timeSlot->start_time} - {$timeSlot->end_time})";
            }
        }

        // Generate booking number
        $bookingNumber = 'FMZ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $booking = Booking::create([
            'booking_number' => $bookingNumber,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'pincode' => $validated['pincode'],
            'service_id' => $service?->id,
            'service_name' => $service ? $service->title : $validated['service_name'],
            'specialist_id' => $specialist?->id,
            'specialist_name' => $specialist ? $specialist->name : ($validated['specialist_name'] ?? null),
            'booking_date' => $validated['date'],
            'time_slot_id' => $timeSlot?->id,
            'time_slot_text' => $slotText,
            'skin_concern' => $validated['msg'] ?? null,
            'status' => 'pending',
            'total_amount' => $totalAmount,
            'payment_status' => 'unpaid',
            'payment_method' => $validated['payment_method'] ?? 'after_visit',
            'consent' => true,
        ]);

        // Send Email Notifications
        EmailNotificationService::notifyBookingCreated($booking);

        return response()->json([
            'success' => true,
            'message' => 'Your booking request has been received! Our team will contact you on WhatsApp to confirm your visit.',
            'booking' => [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'name' => $booking->name,
                'service' => $booking->service_name,
                'date' => $booking->booking_date->format('d M Y'),
                'time' => $booking->time_slot_text,
                'total_amount' => $booking->total_amount,
                'payment_method' => $booking->payment_method,
            ],
        ]);
    }
}
