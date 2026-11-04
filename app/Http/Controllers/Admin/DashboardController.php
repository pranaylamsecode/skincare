<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Specialist;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'today_bookings' => Booking::whereDate('booking_date', $today)->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'total_specialists' => Specialist::where('is_active', true)->count(),
            'total_services' => Service::where('is_active', true)->count(),
        ];

        $recentBookings = Booking::with(['service', 'specialist', 'timeSlot'])
            ->latest()
            ->take(8)
            ->get();

        $todayAppointments = Booking::with(['service', 'specialist', 'timeSlot'])
            ->whereDate('booking_date', $today)
            ->orderBy('time_slot_text')
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'todayAppointments' => $todayAppointments,
        ]);
    }
}
