<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Plan;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Specialist;
use App\Models\TimeSlot;
use App\Services\EmailNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Get available slots for a chosen date and area pincode
     * Auto-detects whether the area is serviceable and filters slots accordingly
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'pincode' => 'nullable|string',
            'specialist_id' => 'nullable|integer',
        ]);

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $pincode = $request->filled('pincode') ? trim($request->pincode) : null;

        // Check area serviceability if 6-digit pincode provided
        $servingSpecialists = null;
        if ($pincode && strlen($pincode) === 6) {
            $servingSpecialists = Specialist::getSpecialistsForPincode($pincode);

            if ($servingSpecialists->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'serviceable' => false,
                    'pincode' => $pincode,
                    'message' => "We currently do not offer at-home skincare service in pincode {$pincode}. Our certified specialists serve Ahmedabad, Gandhinagar, Surat & Rajkot.",
                    'slots' => [],
                ]);
            }
        }

        // Query slots
        $allSlots = TimeSlot::where('is_active', true)
            ->orderBy('period')
            ->orderBy('start_time')
            ->get();

        $availableSlots = [];
        foreach ($allSlots as $slot) {
            if ($servingSpecialists && $servingSpecialists->isNotEmpty()) {
                // Find specialists in this pincode already booked for this slot
                $bookedSpecialistIds = Booking::where('booking_date', $date)
                    ->where('time_slot_id', $slot->id)
                    ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                    ->pluck('specialist_id')
                    ->filter()
                    ->toArray();

                $freeCount = $servingSpecialists->reject(fn($sp) => in_array($sp->id, $bookedSpecialistIds))->count();
                $isAvailable = $freeCount > 0;
                $remaining = $freeCount;
            } else {
                $bookedCount = Booking::where('booking_date', $date)
                    ->where('time_slot_id', $slot->id)
                    ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                    ->count();

                $isAvailable = $bookedCount < $slot->max_bookings;
                $remaining = max(0, $slot->max_bookings - $bookedCount);
            }

            $availableSlots[] = [
                'id' => $slot->id,
                'period' => $slot->period,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'display' => "{$slot->period} ({$slot->start_time} - {$slot->end_time})",
                'is_available' => $isAvailable,
                'remaining' => $remaining,
            ];
        }

        return response()->json([
            'success' => true,
            'serviceable' => true,
            'pincode' => $pincode,
            'specialist_count' => $servingSpecialists ? $servingSpecialists->count() : null,
            'date' => $date,
            'slots' => $availableSlots,
        ]);
    }

    /**
     * Store new customer booking with auto-assigned specialist via round-robin
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
            'date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'nullable|integer',
            'time_slot_text' => 'nullable|string|max:100',
            'msg' => 'nullable|string|max:2000',
            'consent' => 'required|accepted',
            'payment_method' => 'nullable|string', // 'after_visit', 'online', 'razorpay', 'zoho'
        ]);

        $pincode = trim($validated['pincode']);
        $date = Carbon::parse($validated['date'])->format('Y-m-d');
        $timeSlotId = $validated['time_slot_id'] ?? null;

        // Auto-assign specialist using round-robin for this pincode area
        $assignResult = Specialist::findAvailableForPincode($pincode, $date, $timeSlotId);

        if (!$assignResult['serviceable']) {
            return response()->json([
                'success' => false,
                'message' => "We currently do not offer at-home skincare service in pincode {$pincode}. Our certified specialists serve Ahmedabad, Gandhinagar, Surat & Rajkot.",
            ], 422);
        }

        if (!$assignResult['specialist']) {
            return response()->json([
                'success' => false,
                'message' => "All certified specialists serving pincode {$pincode} are fully booked for this time slot. Please choose another slot or date.",
            ], 422);
        }

        $specialist = $assignResult['specialist'];

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

        // Find slot
        $timeSlot = null;
        $slotText = $validated['time_slot_text'] ?? '';
        if ($timeSlotId) {
            $timeSlot = TimeSlot::find($timeSlotId);
            if ($timeSlot) {
                $slotText = "{$timeSlot->period} ({$timeSlot->start_time} - {$timeSlot->end_time})";
            }
        }

        // Generate booking number
        $bookingNumber = 'FMZ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        // Determine payment method and active gateway
        $requestedPayment = $validated['payment_method'] ?? 'after_visit';
        $activeGateway = Setting::get('active_payment_gateway', 'razorpay');
        $zohoCheckoutUrl = null;

        if (in_array($requestedPayment, ['online', 'zoho']) || ($requestedPayment === 'razorpay' && $activeGateway === 'zoho')) {
            $rawZohoUrl = Setting::get('zoho_checkout_url', '');
            $zohoClientId = Setting::get('zoho_client_id', '');
            if (!empty($rawZohoUrl)) {
                $query = http_build_query([
                    'booking_id' => $bookingNumber,
                    'amount' => $totalAmount,
                    'currency' => 'INR',
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? '',
                    'client_id' => $zohoClientId,
                    'service' => $service ? $service->title : $validated['service_name'],
                    'pincode' => $pincode,
                ]);
                $zohoCheckoutUrl = $rawZohoUrl . (str_contains($rawZohoUrl, '?') ? '&' : '?') . $query;
            }
        }

        $booking = Booking::create([
            'booking_number' => $bookingNumber,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'pincode' => $pincode,
            'service_id' => $service?->id,
            'service_name' => $service ? $service->title : $validated['service_name'],
            'specialist_id' => $specialist->id,
            'specialist_name' => $specialist->name,
            'booking_date' => $date,
            'time_slot_id' => $timeSlot?->id,
            'time_slot_text' => $slotText,
            'skin_concern' => $validated['msg'] ?? null,
            'status' => 'pending',
            'total_amount' => $totalAmount,
            'payment_status' => 'unpaid',
            'payment_method' => $requestedPayment,
            'zoho_checkout_url' => $zohoCheckoutUrl,
            'consent' => true,
        ]);

        $isOnline = in_array($booking->payment_method, ['online', 'razorpay', 'zoho']);

        // Send Email Notifications immediately only for Pay After Visit
        // For online payment, notification is sent after payment is successfully verified
        if (!$isOnline) {
            EmailNotificationService::notifyBookingCreated($booking);
        }

        return response()->json([
            'success' => true,
            'is_online_payment' => $isOnline,
            'message' => $isOnline 
                ? "Booking request initiated. Please complete your payment." 
                : "Appointment scheduled! Specialist {$specialist->name} has been assigned to your visit in pincode {$pincode}.",
            'booking' => [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'name' => $booking->name,
                'service' => $booking->service_name,
                'date' => $booking->booking_date->format('d M Y'),
                'time' => $booking->time_slot_text,
                'pincode' => $booking->pincode,
                'specialist_name' => $specialist->name,
                'specialist_qualification' => $specialist->qualification,
                'total_amount' => $booking->total_amount,
                'payment_method' => $booking->payment_method,
                'payment_status' => $booking->payment_status,
                'status' => $booking->status,
                'zoho_checkout_url' => $zohoCheckoutUrl,
            ],
            'assigned_specialist' => [
                'id' => $specialist->id,
                'name' => $specialist->name,
                'qualification' => $specialist->qualification,
                'location' => $specialist->location,
                'badge' => $specialist->badge,
            ],
        ]);
    }

    /**
     * Switch an unpaid/pending online booking to Pay After Visit (Cash / UPI)
     */
    public function switchToPayAfterVisit($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This booking has already been cancelled.',
            ], 422);
        }

        $booking->update([
            'payment_method' => 'after_visit',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        // Send booking confirmation email now that customer has confirmed cash payment
        EmailNotificationService::notifyBookingCreated($booking);

        return response()->json([
            'success' => true,
            'message' => "Appointment scheduled! Specialist {$booking->specialist_name} has been assigned to your visit in pincode {$booking->pincode}. You can pay after your visit via UPI or Cash.",
            'booking' => [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'name' => $booking->name,
                'service' => $booking->service_name,
                'date' => $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A',
                'time' => $booking->time_slot_text,
                'pincode' => $booking->pincode,
                'specialist_name' => $booking->specialist_name,
                'total_amount' => $booking->total_amount,
                'payment_method' => 'after_visit',
                'payment_status' => 'unpaid',
                'status' => 'pending',
            ],
        ]);
    }
}
