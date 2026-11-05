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
        $user = auth()->user();
        $isStaff = $user && $user->isStaff();

        $query = Booking::with(['service', 'specialist', 'timeSlot'])->latest();

        // Staff sees only their assigned bookings
        if ($isStaff && $user->specialist_id) {
            $query->where('specialist_id', $user->specialist_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if (!$isStaff && $request->filled('specialist_id')) {
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
            'isStaff' => $isStaff,
            'filters' => $request->only(['status', 'payment_status', 'specialist_id', 'date', 'search']),
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with(['service', 'specialist', 'timeSlot'])->findOrFail($id);
        $user = auth()->user();

        if ($user && $user->isStaff() && $booking->specialist_id != $user->specialist_id) {
            abort(403, 'Unauthorized. You can only view bookings assigned to you.');
        }

        $specialists = Specialist::where('is_active', true)->get(['id', 'name']);
        $timeSlots = TimeSlot::where('is_active', true)->get();

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => $booking,
            'specialists' => $specialists,
            'timeSlots' => $timeSlots,
            'isStaff' => $user?->isStaff() ?? false,
        ]);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $user = auth()->user();

        if ($user && $user->isStaff() && $booking->specialist_id != $user->specialist_id) {
            abort(403, 'Unauthorized. You can only update bookings assigned to you.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'specialist_id' => 'nullable|exists:specialists,id',
            'payment_status' => 'nullable|in:unpaid,pending,paid,failed,refunded',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $prevStatus = $booking->status;

        if ($user && $user->isStaff()) {
            // Staff cannot reassign to another specialist
            unset($validated['specialist_id']);
        } elseif (!empty($validated['specialist_id'])) {
            $specialist = Specialist::find($validated['specialist_id']);
            $validated['specialist_name'] = $specialist?->name;
        }

        $booking->update($validated);

        // Send notifications if status changed
        if ($prevStatus !== 'confirmed' && $booking->status === 'confirmed') {
            EmailNotificationService::notifyBookingConfirmed($booking);
        }

        return back()->with('success', 'Booking updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user && $user->isStaff()) {
            abort(403, 'Unauthorized. Staff members cannot delete bookings.');
        }

        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }
}
