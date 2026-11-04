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
        $timeSlots = TimeSlot::with('specialist')->orderBy('period')->orderBy('start_time')->get();
        $specialists = Specialist::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Admin/TimeSlots/Index', [
            'timeSlots' => $timeSlots,
            'specialists' => $specialists,
        ]);
    }

    public function store(Request $request)
    {
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

        TimeSlot::create($validated);

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot created successfully.');
    }

    public function update(Request $request, $id)
    {
        $slot = TimeSlot::findOrFail($id);

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

        $slot->update($validated);

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot updated successfully.');
    }

    public function destroy($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $slot->delete();

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot deleted successfully.');
    }
}
