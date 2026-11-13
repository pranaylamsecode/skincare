<?php

namespace App\Http\Controllers;

use App\Models\Concern;
use App\Models\CmeOnboarding;
use App\Models\Device;
use App\Models\Faq;
use App\Models\Page;
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

    public function page($slug)
    {
        $slug = trim($slug, '/');
        $settings = [];
        try {
            $settings = Setting::all()->pluck('value', 'key')->toArray();
        } catch (\Throwable $e) {}

        $page = null;
        try {
            $page = Page::where('slug', $slug)->first();
        } catch (\Throwable $e) {}

        if (!$page) {
            $fallbacks = [
                'privacy-policy' => [
                    'title' => 'Privacy Policy',
                    'slug' => 'privacy-policy',
                    'meta_description' => 'Privacy Policy for FMZ Glow at-home skincare services. How we protect your data and personal information.',
                    'content' => "### 1. Introduction\nWelcome to FMZ Glow (\"FMZ India\", \"we\", \"our\", or \"us\"). We provide certified at-home skincare and wellness services delivered by trained women specialists in Ahmedabad, Gandhinagar, Surat, Rajkot, and surrounding areas. We respect your privacy and are committed to protecting your personal data.\n\n### 2. Information We Collect\nWe collect only the information necessary to provide, confirm, and deliver our at-home skincare visits safely:\n- **Contact Details:** Your full name, mobile / WhatsApp number, and email address.\n- **Visit Address:** Your residential address, locality, and postal pincode so our certified specialist can navigate to your home.\n- **Skin & Health Information:** Optional skin concerns, allergies, or conditions shared during booking to help your specialist prepare the right Nu Skin products and ensure treatment safety (e.g., pregnancy or medical implants for Galvanic Spa).\n- **Communication History:** Booking requests, confirmations, and customer support messages exchanged via WhatsApp or email.\n\n### 3. How We Use Your Information\nYour information is used exclusively for:\n- Scheduling appointments and assigning a verified woman specialist to your home.\n- Sending written booking confirmations, specialist identity, exact arrival time, and total price via WhatsApp and email.\n- Calculating travel distance (free within 8 km; transparent nominal travel confirmed in writing for farther distances).\n- Providing aftercare tips and voluntary customer service follow-ups.\n\n### 4. Specialist Verification & Confidentiality\nAll FMZ Glow specialists are verified women professionals with science, pharmacy, or nursing backgrounds. Your residential address and phone number are shared solely with the assigned specialist for the purpose of completing your visit. We never sell, rent, or trade your personal information to third-party marketers or advertisers.\n\n### 5. Secure Payments\nWe offer **Pay After Your Visit** via UPI or Cash directly to your specialist once you are fully satisfied with your treatment. For customers choosing to pay online, payment transactions are encrypted and processed through RBI-approved gateways (Razorpay / Zoho Payments). We do not store your credit card numbers, debit card details, or UPI PINs.\n\n### 6. Data Security & Retention\nWe maintain administrative and technical safeguards to keep your personal data secure. Booking records are retained only as long as necessary to satisfy operational, accounting, and legal requirements.\n\n### 7. Contact Us\nIf you have any questions about this Privacy Policy or wish to access, correct, or delete your personal details, please contact us:\n- **WhatsApp / Phone:** +91 73037 06047\n- **Email:** fmz@fmzau.com.au\n- **Operating Hours:** 9:00 AM – 8:00 PM (Monday – Sunday)",
                ],
                'terms-and-conditions' => [
                    'title' => 'Terms & Conditions',
                    'slug' => 'terms-and-conditions',
                    'meta_description' => 'Terms and Conditions for FMZ Glow at-home skincare bookings and appointments.',
                    'content' => "### 1. Overview & Agreement\nBy booking an appointment through FMZ Glow (myfmz.com), WhatsApp, or telephone, you agree to these Terms and Conditions. Please review them carefully before scheduling a visit.\n\n### 2. Women Specialists Guarantee\n- **Exclusively Certified Women Specialists:** All at-home treatments are performed exclusively by certified women specialists with pharmacy, nursing, or science credentials.\n- **Zero Tolerance Policy:** We maintain a strict zero-tolerance policy against any inappropriate behavior, harassment, or unsafe conditions directed toward our specialists. The specialist reserves the unconditional right to leave immediately if safety or dignity is compromised, and legal action may be pursued where warranted.\n\n### 3. Genuine Nu Skin Products & Safety Protocols\n- **Authentic & Sealed:** We exclusively use authentic, genuine Nu Skin devices (LumiSpa iO, Galvanic Spa, Prysm iO) and professional skincare formulations. Products are unsealed in front of you.\n- **Pre-Visit Skin & Health Consultation:** Every visit begins with a brief consultation. Before using the Galvanic Spa, your specialist will verify contraindications (such as pregnancy, pacemakers, metal dental/facial implants, or acute skin infections). If contraindicated, she will skip or adapt that step for your safety.\n\n### 4. Appointment Booking & Confirmation\n- **Written Confirmation on WhatsApp:** Once you submit a booking request, our coordination team confirms your specialist's name, phone number, confirmed date, time slot, and total price in writing on WhatsApp within 30 minutes during operating hours (9:00 AM to 8:00 PM).\n- **Service Availability:** Services are offered across Ahmedabad, Gandhinagar, Surat, and Rajkot within serviceable pincodes.\n\n### 5. Pricing, Travel & Payment\n- **Transparent Pricing:** The price confirmed in writing is the final price. There are no hidden setup fees or surprise charges.\n- **Travel Policy:** Travel is completely **free within 8 km** of the assigned specialist. For locations beyond 8 km, any modest travel surcharge will be calculated transparently and confirmed with you in writing before dispatching the specialist.\n- **Pay After Your Visit:** You may pay by UPI or Cash after your visit once you are satisfied with your treatment. If you choose online prepayment, receipts are delivered electronically to your email.\n\n### 6. Modifications to Service\nFMZ Glow reserves the right to modify service offerings, pricing, or terms with prior notice published on this website. Confirmed bookings will always be honored at the price confirmed at the time of scheduling.",
                ],
                'cancellation-and-refunds' => [
                    'title' => 'Cancellation and Refunds',
                    'slug' => 'cancellation-and-refunds',
                    'meta_description' => 'Cancellation and refund policies for FMZ Glow appointments and multi-visit plans.',
                    'content' => "### 1. Free Cancellation & Rescheduling\nWe understand that plans can change. We offer complete flexibility:\n- **Free Cancellation:** You can cancel or reschedule your appointment with **no fee or penalty** up to **4 hours** before your scheduled appointment time.\n- **How to Reschedule or Cancel:** Simply message our team on WhatsApp at **+91 73037 06047** or call us with your booking ID.\n\n### 2. Late Cancellations & Doorstep No-Shows\n- If an appointment is cancelled with less than 2 hours' notice after the specialist has already commenced travel or arrived at your doorstep, a nominal travel convenience fee of **₹150** may be requested to compensate the specialist for her travel time.\n- If you are unavailable or unresponsive at the scheduled address for more than 20 minutes past the scheduled time slot, the visit may be marked as a no-show.\n\n### 3. Refund Policy for Online Prepayments\n- If you prepaid online and cancel at least 4 hours before the appointment, a **100% full refund** will be automatically initiated to your original payment method (Bank Account, UPI, or Credit/Debit Card).\n- Refunds typically reflect in your account within **5 to 7 business days**, depending on your issuing bank.\n\n### 4. Multi-Visit Plans (e.g. Glow 3-Visit Plan)\n- **Validity:** The Glow 3-Visit Plan (₹2,799 / ₹933 per visit) is valid for **90 days** from the date of the first visit.\n- **Transferability:** Visits can be shared with immediate family members (e.g., mother, sister, or daughter) residing at the same address.\n- **Plan Cancellation & Pro-Rata Refunds:** If you wish to discontinue a 3-visit plan after completing 1 visit, the completed session will be billed at the standard individual visit rate (₹999), and the entire remaining balance (₹1,800) will be promptly refunded to you.\n\n### 5. Satisfaction Guarantee\nYour comfort, safety, and glow are our highest priorities. If you are not completely satisfied with your specialist's service or if any product causes unexpected discomfort, please alert us within 24 hours of your visit. Our care coordinator will arrange a complimentary follow-up or provide an appropriate resolution.\n\n### 6. Support Contact\nFor all cancellation, rescheduling, and refund requests:\n- **WhatsApp Support:** +91 73037 06047\n- **Email:** fmz@fmzau.com.au\n- **Response Time:** Typically within 30 minutes (9:00 AM – 8:00 PM)",
                ],
            ];

            if (!isset($fallbacks[$slug])) {
                abort(404);
            }

            $page = (object) $fallbacks[$slug];
        }

        return Inertia::render('Policy', [
            'page' => $page,
            'settings' => $settings,
        ]);
    }

    public function privacyPolicy()
    {
        return $this->page('privacy-policy');
    }

    public function terms()
    {
        return $this->page('terms-and-conditions');
    }

    public function cancellationRefunds()
    {
        return $this->page('cancellation-and-refunds');
    }

    public function becomeCme()
    {
        $settings = [];
        try {
            $settings = Setting::all()->pluck('value', 'key')->toArray();
        } catch (\Throwable $e) {}

        return Inertia::render('BecomeCme', [
            'settings' => $settings,
        ]);
    }

    public function submitCme(Request $request)
    {
        $validated = $request->validate([
            'programmes' => 'required|array|min:1',
            'nursing_qualification' => 'nullable|string|max:100',
            'pincode' => 'required|string|regex:/^[1-9]\d{5}$/',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'age_group' => 'required|string|not_in:Under 18',
            'gender' => 'required|string|max:50',
            'highest_qualification' => 'required|string|max:100',
            'vehicle' => 'required|string|max:50',
            'driving_licence' => 'required|string|max:20',
            'mobile' => 'required|string|regex:/^[6-9]\d{9}$/',
            'is_whatsapp_same' => 'nullable|boolean',
            'whatsapp_number' => 'nullable|string',
            'email' => 'nullable|email|max:150',
            'referral_source' => 'required|string|max:100',
            'referral_name' => 'nullable|string|max:150',
            'referral_contact' => 'nullable|string|max:100',
            'referral_org' => 'nullable|string|max:150',
            'confirmed_accuracy' => 'required|accepted',
            'confirmed_conditions' => 'required|accepted',
            'confirmed_contact' => 'required|accepted',
        ]);

        $yearMonth = date('ym');
        $random = mt_rand(1000, 9999);
        $regId = "FMZ-CME-{$yearMonth}-{$random}";

        try {
            while (CmeOnboarding::where('registration_id', $regId)->exists()) {
                $random = mt_rand(1000, 9999);
                $regId = "FMZ-CME-{$yearMonth}-{$random}";
            }

            $validated['registration_id'] = $regId;
            $validated['is_whatsapp_same'] = $request->boolean('is_whatsapp_same', true);
            if ($validated['is_whatsapp_same']) {
                $validated['whatsapp_number'] = $validated['mobile'];
            }

            $record = CmeOnboarding::create($validated);
        } catch (\Throwable $e) {
            // DB fallback in case table not migrated
            $record = (object)[
                'full_name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'first_name' => $validated['first_name'],
            ];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'registration_id' => $regId,
                'applicant_name' => $record->full_name,
                'message' => "Thank you, {$record->first_name}! Your registration has been received.",
            ]);
        }

        return back()->with([
            'success' => true,
            'registration_id' => $regId,
            'applicant_name' => $record->full_name,
        ]);
    }
}
