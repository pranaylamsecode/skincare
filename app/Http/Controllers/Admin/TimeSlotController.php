<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialist;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimeSlotController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isStaff = $user && $user->isStaff();

        $query = TimeSlot::with('specialist')->orderBy('period')->orderBy('start_time');

        if ($isStaff && $user->specialist_id) {
            // Staff sees their own specialist time slots + general slots
            $query->where(function ($q) use ($user) {
                $q->where('specialist_id', $user->specialist_id)
                  ->orWhereNull('specialist_id');
            });
            $specialists = Specialist::where('id', $user->specialist_id)->get(['id', 'name']);
        } else {
            $specialists = Specialist::where('is_active', true)->get(['id', 'name']);
        }

        $timeSlots = $query->get();

        return Inertia::render('Admin/TimeSlots/Index', [
            'timeSlots' => $timeSlots,
            'specialists' => $specialists,
            'isStaff' => $isStaff,
            'currentSpecialistId' => $user?->specialist_id,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isStaff = $user && $user->isStaff();

        $validated = $request->validate([
            'period' => 'required|in:Morning,Afternoon,Evening',
            'start_time' => 'required|string|max:20',
            'end_time' => 'required|string|max:20',
            'specialist_id' => 'nullable|exists:specialists,id',
            'day_of_week' => 'nullable|string|max:20',
            'specific_date' => 'nullable|date',
            'max_bookings' => 'required|integer|min:1|max:50',
            'is_active' => 'boolean',
        ]);

        if ($isStaff) {
            if (!$user->specialist_id) {
                return back()->with('error', 'Your staff account is not linked to a specialist profile.');
            }
            // Automatically assign slot to this staff member's specialist profile
            $validated['specialist_id'] = $user->specialist_id;
        }

        TimeSlot::create($validated);

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot added successfully.');
    }

    public function update(Request $request, $id)
    {
        $slot = TimeSlot::findOrFail($id);
        $user = auth()->user();
        $isStaff = $user && $user->isStaff();

        if ($isStaff && $slot->specialist_id != $user->specialist_id) {
            abort(403, 'Unauthorized. Staff members can only manage their own time slots.');
        }

        $validated = $request->validate([
            'period' => 'required|in:Morning,Afternoon,Evening',
            'start_time' => 'required|string|max:20',
            'end_time' => 'required|string|max:20',
            'specialist_id' => 'nullable|exists:specialists,id',
            'day_of_week' => 'nullable|string|max:20',
            'specific_date' => 'nullable|date',
            'max_bookings' => 'required|integer|min:1|max:50',
            'is_active' => 'boolean',
        ]);

        if ($isStaff) {
            $validated['specialist_id'] = $user->specialist_id;
        }

        $slot->update($validated);

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot updated successfully.');
    }

    public function destroy($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $user = auth()->user();
        $isStaff = $user && $user->isStaff();

        if ($isStaff && $slot->specialist_id != $user->specialist_id) {
            abort(403, 'Unauthorized. Staff members can only delete their own time slots.');
        }

        $slot->delete();

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot deleted successfully.');
    }
}
