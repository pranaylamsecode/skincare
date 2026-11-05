<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Concern;
use App\Models\Device;
use App\Models\Faq;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Specialist;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Helper to resolve image URL
     */
    private function formatImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return url(ltrim($path, '/'));
    }

    /**
     * Complete Home Feed API for Flutter App
     */
    public function home()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        $services = Service::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($s) {
                $s->image_url = $this->formatImageUrl($s->image);
                return $s;
            });

        $specialists = Specialist::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($sp) {
                $sp->photo_url = $this->formatImageUrl($sp->photo);
                return $sp;
            });

        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $devices = Device::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($d) {
                $d->image_url = $this->formatImageUrl($d->image);
                return $d;
            });

        $dailyProducts = Product::where('is_active', true)
            ->where('category', 'daily')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($p) {
                $p->image_url = $this->formatImageUrl($p->image);
                return $p;
            });

        $wellnessProducts = Product::where('is_active', true)
            ->where('category', 'wellness')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($p) {
                $p->image_url = $this->formatImageUrl($p->image);
                return $p;
            });

        $concerns = Concern::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $results = Review::where('is_active', true)
            ->where('is_featured_result', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($r) {
                $r->image_after_url = $this->formatImageUrl($r->image_after);
                $r->image_before_url = $this->formatImageUrl($r->image_before);
                return $r;
            });

        $reviews = Review::where('is_active', true)
            ->where('is_featured_result', false)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($r) {
                $r->image_after_url = $this->formatImageUrl($r->image_after);
                $r->image_before_url = $this->formatImageUrl($r->image_before);
                return $r;
            });

        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $timeSlots = TimeSlot::where('is_active', true)
            ->orderBy('start_time')
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'period' => $slot->period,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'display' => "{$slot->period} ({$slot->start_time} - {$slot->end_time})",
                    'specialist_id' => $slot->specialist_id,
                ];
            });

        return response()->json([
            'success' => true,
            'settings' => $settings,
            'services' => $services,
            'specialists' => $specialists,
            'plans' => $plans,
            'devices' => $devices,
            'daily_products' => $dailyProducts,
            'wellness_products' => $wellnessProducts,
            'concerns' => $concerns,
            'results' => $results,
            'reviews' => $reviews,
            'faqs' => $faqs,
            'time_slots' => $timeSlots,
        ]);
    }

    /**
     * List all services
     */
    public function getServices()
    {
        $services = Service::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($s) {
                $s->image_url = $this->formatImageUrl($s->image);
                return $s;
            });

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    /**
     * Single service details
     */
    public function getServiceDetail($id)
    {
        $service = Service::where('is_active', true)->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('slug', $id);
        })->firstOrFail();

        $service->image_url = $this->formatImageUrl($service->image);

        // Fetch specialists that can perform this service
        $specialists = Specialist::where('is_active', true)->get()->map(function ($sp) {
            $sp->photo_url = $this->formatImageUrl($sp->photo);
            return $sp;
        });

        return response()->json([
            'success' => true,
            'service' => $service,
            'specialists' => $specialists,
        ]);
    }

    /**
     * List specialists with optional city filtering
     */
    public function getSpecialists(Request $request)
    {
        $query = Specialist::where('is_active', true);

        if ($request->filled('city')) {
            $query->where('city', strtolower($request->city));
        }

        $specialists = $query->orderBy('sort_order')->get()->map(function ($sp) {
            $sp->photo_url = $this->formatImageUrl($sp->photo);
            return $sp;
        });

        return response()->json([
            'success' => true,
            'data' => $specialists,
        ]);
    }

    /**
     * List packages/plans
     */
    public function getPlans()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * List patented devices
     */
    public function getDevices()
    {
        $devices = Device::where('is_active', true)->orderBy('sort_order')->get()->map(function ($d) {
            $d->image_url = $this->formatImageUrl($d->image);
            return $d;
        });
        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    /**
     * List products
     */
    public function getProducts(Request $request)
    {
        $query = Product::where('is_active', true);
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->orderBy('sort_order')->get()->map(function ($p) {
            $p->image_url = $this->formatImageUrl($p->image);
            return $p;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * List skin concerns
     */
    public function getConcerns()
    {
        $concerns = Concern::where('is_active', true)->orderBy('sort_order')->get();
        return response()->json([
            'success' => true,
            'data' => $concerns,
        ]);
    }

    /**
     * List reviews and testimonials
     */
    public function getReviews()
    {
        $reviews = Review::where('is_active', true)->orderBy('sort_order')->get()->map(function ($r) {
            $r->image_after_url = $this->formatImageUrl($r->image_after);
            $r->image_before_url = $this->formatImageUrl($r->image_before);
            return $r;
        });
        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    /**
     * List FAQs
     */
    public function getFaqs()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();
        return response()->json([
            'success' => true,
            'data' => $faqs,
        ]);
    }

    /**
     * Track booking by booking number or id
     */
    public function getBooking($identifier)
    {
        $booking = Booking::with(['service', 'specialist', 'timeSlot'])
            ->where('booking_number', $identifier)
            ->orWhere('id', $identifier)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found with reference ' . $identifier,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'booking' => $booking,
        ]);
    }

    /**
     * Search user bookings by phone number
     */
    public function getBookingsByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // Normalize phone search: remove spaces, plus, dashes
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        // Take last 10 digits
        $last10 = substr($phone, -10);

        $bookings = Booking::with(['service', 'specialist', 'timeSlot'])
            ->where(function ($q) use ($request, $last10) {
                $q->where('phone', 'like', "%{$last10}%")
                  ->orWhere('phone', $request->phone);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $bookings->count(),
            'bookings' => $bookings,
        ]);
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be cancelled because it is already ' . $booking->status,
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'admin_notes' => trim(($booking->admin_notes ?? '') . "\nCancelled by customer. Reason: " . ($request->reason ?? 'Not specified')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking #' . $booking->booking_number . ' has been cancelled successfully.',
            'booking' => $booking,
        ]);
    }

    /**
     * App settings
     */
    public function getSettings()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return response()->json([
            'success' => true,
            'settings' => $settings,
        ]);
    }
}

