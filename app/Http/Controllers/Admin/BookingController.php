<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Specialist;
use App\Models\TimeSlot;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['service', 'specialist', 'timeSlot'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('specialist_id')) {
            $query->where('specialist_id', $request->specialist_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('booking_number', 'like', "%{$s}%")
                  ->orWhere('pincode', 'like', "%{$s}%")
                  ->orWhere('service_name', 'like', "%{$s}%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $specialists = Specialist::where('is_active', true)->get(['id', 'name']);
        $timeSlots = TimeSlot::where('is_active', true)->get();

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'specialists' => $specialists,
            'timeSlots' => $timeSlots,
            'filters' => $request->only(['status', 'payment_status', 'specialist_id', 'date', 'search']),
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with(['service', 'specialist', 'timeSlot'])->findOrFail($id);
        $specialists = Specialist::where('is_active', true)->get(['id', 'name', 'phone']);
        $timeSlots = TimeSlot::where('is_active', true)->get();

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => $booking,
            'specialists' => $specialists,
            'timeSlots' => $timeSlots,
        ]);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'specialist_id' => 'nullable|exists:specialists,id',
            'time_slot_id' => 'nullable|exists:time_slots,id',
            'time_slot_text' => 'nullable|string|max:100',
            'payment_status' => 'required|in:unpaid,pending,paid,failed,refunded',
            'total_amount' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string|max:1000',
            'send_notification' => 'nullable|boolean',
        ]);

        $oldStatus = $booking->status;

        // If specialist assigned
        if (!empty($validated['specialist_id'])) {
            $specialist = Specialist::find($validated['specialist_id']);
            $validated['specialist_name'] = $specialist?->name;
        }

        // If time slot assigned
        if (!empty($validated['time_slot_id'])) {
            $slot = TimeSlot::find($validated['time_slot_id']);
            if ($slot) {
                $validated['time_slot_text'] = "{$slot->period} ({$slot->start_time} - {$slot->end_time})";
            }
        }

        $booking->update($validated);

        // If status changed to confirmed and send_notification requested
        if ($request->boolean('send_notification') || ($oldStatus !== 'confirmed' && $validated['status'] === 'confirmed')) {
            EmailNotificationService::notifyBookingConfirmed($booking);
        }

        return redirect()->back()->with('success', 'Booking updated successfully.');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }
}
