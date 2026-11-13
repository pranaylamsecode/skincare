<?php

namespace App\Http\Controllers;

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
use Inertia\Inertia;

class FrontendController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $genderFilter = $settings['specialist_gender_filter'] ?? 'female';

        $services = Service::where('is_active', true)->orderBy('sort_order')->get();

        $specialistsQuery = Specialist::where('is_active', true);
        if ($genderFilter && in_array($genderFilter, ['female', 'male'])) {
            $specialistsQuery->where('gender', $genderFilter);
        }
        $specialists = $specialistsQuery->orderBy('sort_order')->get();

        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get()->map(function ($plan) {
            if (str_contains($plan->title, '3-Visit') || (float)$plan->price == 3299) {
                if ((float)$plan->price != 2799 || str_contains($plan->description, 'Save ₹298') || str_contains($plan->description, 'LumiSpa')) {
                    $plan->title = 'Glow 3-Visit Plan';
                    $plan->price = 2799;
                    $plan->price_subtext = '₹933 per visit';
                    $plan->description = 'Three Glow Facials with the same specialist, spaced to suit your skin. Valid 90 days.';
                    $plan->save();
                }
            }
            return $plan;
        });
        $devices = Device::where('is_active', true)->orderBy('sort_order')->get();
        $dailyProducts = Product::where('is_active', true)->where('category', 'daily')->orderBy('sort_order')->get();
        $wellnessProducts = Product::where('is_active', true)->where('category', 'wellness')->orderBy('sort_order')->get();
        $concerns = Concern::where('is_active', true)
            ->where('quote', 'not like', '%event%')
            ->where('quote', 'not like', '%routine%')
            ->orderBy('sort_order')
            ->take(4)
            ->get();
        $results = Review::where('is_active', true)->where('is_featured_result', true)->orderBy('sort_order')->get();
        $reviews = Review::where('is_active', true)->where('is_featured_result', false)->orderBy('sort_order')->get();
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();

        // Default standard active time slots
        $timeSlots = TimeSlot::where('is_active', true)->orderBy('start_time')->get()->map(function ($slot) {
            return [
                'id' => $slot->id,
                'period' => $slot->period,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'display' => "{$slot->period} ({$slot->start_time} - {$slot->end_time})",
                'specialist_id' => $slot->specialist_id,
            ];
        });

        return Inertia::render('Home', [
            'services' => $services,
            'specialists' => $specialists,
            'plans' => $plans,
            'devices' => $devices,
            'dailyProducts' => $dailyProducts,
            'wellnessProducts' => $wellnessProducts,
            'concerns' => $concerns,
            'results' => $results,
            'reviews' => $reviews,
            'faqs' => $faqs,
            'settings' => $settings,
            'timeSlots' => $timeSlots,
        ]);
    }
}
