<?php

namespace Database\Seeders;

use App\Models\Concern;
use App\Models\Device;
use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Specialist;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin user
        User::firstOrCreate(
            ['email' => 'admin@fmzglow.com'],
            [
                'name' => 'FMZ Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Settings
        $settings = [
            'site_name' => 'FMZ Glow',
            'business_email' => 'fmz@fmzau.com.au',
            'support_phone' => '+917303706047',
            'whatsapp_number' => '+917303706047',
            'whatsapp_default_message' => "Hi FMZ Glow, I'd like to know more about your at-home skincare services.",
            'topbar_text' => 'Launch offer: Nu Skin LumiSpa Glow Facial <b>₹999</b> <s>₹1,199</s> · limited period · T&C apply',
            'hero_eyebrow' => 'At-home skincare by FMZ India',
            'hero_title' => 'Skincare that<br><em>understands</em> you.',
            'hero_lead' => 'We listen to your skin concerns first. Then a certified woman specialist brings professional Nu Skin care to your home, explains every step, and leaves you with a routine that fits your life.',
            'hero_tagline' => 'Your Glow. Your Home. Your Time.',
            'razorpay_key' => 'rzp_test_placeholder',
            'razorpay_secret' => 'rzp_secret_placeholder',
            'razorpay_enabled' => '0',
            'currency' => 'INR',
        ];

        foreach ($settings as $key => $val) {
            Setting::firstOrCreate(['key' => $key], ['value' => $val, 'group' => 'general']);
        }

        // 3. Services
        $services = [
            [
                'title' => 'FMZ Glow Cleanse',
                'slug' => 'fmz-glow-cleanse',
                'tag' => 'A gentle start',
                'tag_style' => 'normal',
                'description' => 'A refreshing cleansing and hydration-focused care, using approved products suited to your skin.',
                'features' => [
                    'Skin and product suitability consultation',
                    'Gentle cleansing with LumiSpa iO',
                    'Helps remove daily dirt and impurities',
                    'Skin feels clean, soft and refreshed',
                    'Moisturising finish',
                    'Simple aftercare and skincare tips',
                ],
                'price' => 799,
                'original_price' => null,
                'launch_label' => null,
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=900&q=80',
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'title' => 'FMZ Galvanic Glow Facial',
                'slug' => 'fmz-galvanic-glow-facial',
                'tag' => 'Signature · Launch offer',
                'tag_style' => 'tan',
                'description' => 'A premium facial experience using the Nu Skin Galvanic Spa device and selected skincare products.',
                'features' => [
                    'Skin consultation and suitability check',
                    'Facial treatment using the Galvanic Spa device',
                    'Uses selected Nu Skin skincare products',
                    'Helps skin feel soft and smooth',
                    'Leaves skin feeling refreshed and nourished',
                    'Moisturising finish and home-care tips',
                ],
                'price' => 999,
                'original_price' => 1199,
                'launch_label' => 'launch price',
                'image' => 'images/fmz-glow-facial.png',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'FMZ Prysm iO Radiance Ritual',
                'slug' => 'fmz-prysm-io-radiance-ritual',
                'tag' => 'Premium',
                'tag_style' => 'dark',
                'description' => 'A personalised wellness experience combining a quick skin antioxidant scan with tailored skincare and wellness guidance.',
                'features' => [
                    'Extended personal consultation',
                    'Quick 15-second finger scan',
                    'Get your personal Prysm Score',
                    'Understand your skin\'s antioxidant level',
                    'Track changes in your score over time',
                    'Personalised wellness and skincare guidance',
                ],
                'price' => 1699,
                'original_price' => null,
                'launch_label' => null,
                'image' => 'images/fmz-radiance-ritual.png',
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($services as $svc) {
            Service::firstOrCreate(['slug' => $svc['slug']], $svc);
        }

        // 4. Specialists
        $specialists = [
            [
                'name' => 'Dhruvi Jyani',
                'qualification' => 'B.Sc. in Biochemistry',
                'location' => 'Paldi & Central Ahmedabad (380007)',
                'pincode' => '380007',
                'city' => 'ahmedabad',
                'languages' => ['Gujarati', 'Hindi', 'English'],
                'photo' => 'images/Dhruvi.png',
                'badge' => 'Verified Specialist',
                'sort_order' => 1,
            ],
            [
                'name' => 'Dolly Patel',
                'qualification' => 'Master\'s Graduate (M.Com)',
                'location' => 'Odhav & East Ahmedabad (382415)',
                'pincode' => '382415',
                'city' => 'ahmedabad',
                'languages' => ['Gujarati', 'Hindi', 'English'],
                'photo' => 'images/Dolly.png',
                'badge' => 'Verified Specialist',
                'sort_order' => 2,
            ],
            [
                'name' => 'Minakshi Savaliya',
                'qualification' => 'B.A. Background',
                'location' => 'Vastral & Ring Road (382418)',
                'pincode' => '382418',
                'city' => 'ahmedabad',
                'languages' => ['Gujarati', 'Hindi', 'English'],
                'photo' => 'images/Minakshi.png',
                'badge' => 'Verified Specialist',
                'sort_order' => 3,
            ],
            [
                'name' => 'Kelee Vanzara',
                'qualification' => 'B.Sc. in Chemistry',
                'location' => 'Navrangpura & University Area (380009)',
                'pincode' => '380009',
                'city' => 'ahmedabad',
                'languages' => ['Gujarati', 'Hindi'],
                'photo' => 'images/Kelee.png',
                'badge' => 'Verified Specialist',
                'sort_order' => 4,
            ],
            [
                'name' => 'Dharvi Thummar',
                'qualification' => 'D.Pharm / B.Pharm',
                'location' => 'Navrangpura / West Ahmedabad',
                'pincode' => '380009',
                'city' => 'ahmedabad',
                'languages' => ['Gujarati', 'Hindi', 'English'],
                'photo' => 'images/Dharvi.jpg',
                'badge' => 'Registered Pharmacist',
                'sort_order' => 5,
            ],
            [
                'name' => 'Param Patel',
                'qualification' => 'B.Sc. in Zoology',
                'location' => 'PDEU Road, Gandhinagar (382421)',
                'pincode' => '382421',
                'city' => 'gandhinagar',
                'languages' => ['Gujarati', 'Hindi', 'English'],
                'photo' => 'images/Param.png',
                'badge' => 'Verified Specialist',
                'sort_order' => 6,
            ],
            [
                'name' => 'Vishwa Malaviya',
                'qualification' => 'D.Pharm / B.Pharm',
                'location' => 'Hirabag, Surat',
                'pincode' => '395006',
                'city' => 'surat',
                'languages' => ['Gujarati', 'Hindi', 'English'],
                'photo' => 'images/Vishwa.jpg',
                'badge' => 'Registered Pharmacist',
                'sort_order' => 7,
            ],
            [
                'name' => 'Diya Khimsuriya',
                'qualification' => 'Nursing Background',
                'location' => 'Ambedkar Nagar, Rajkot (360004)',
                'pincode' => '360004',
                'city' => 'rajkot',
                'languages' => ['Gujarati', 'Hindi', 'English'],
                'photo' => 'images/Diya.png',
                'badge' => 'Certified Care Specialist',
                'sort_order' => 8,
            ],
        ];

        foreach ($specialists as $spec) {
            Specialist::firstOrCreate(['name' => $spec['name']], $spec);
        }

        // 5. Time Slots (Standard recurring daily time slots for morning, afternoon, evening)
        $standardSlots = [
            ['period' => 'Morning', 'start_time' => '09:00 AM', 'end_time' => '10:30 AM'],
            ['period' => 'Morning', 'start_time' => '10:30 AM', 'end_time' => '12:00 PM'],
            ['period' => 'Afternoon', 'start_time' => '01:00 PM', 'end_time' => '02:30 PM'],
            ['period' => 'Afternoon', 'start_time' => '02:30 PM', 'end_time' => '04:00 PM'],
            ['period' => 'Evening', 'start_time' => '04:30 PM', 'end_time' => '06:00 PM'],
            ['period' => 'Evening', 'start_time' => '06:00 PM', 'end_time' => '07:30 PM'],
        ];

        foreach ($standardSlots as $slot) {
            TimeSlot::firstOrCreate(
                [
                    'period' => $slot['period'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'specialist_id' => null,
                ],
                [
                    'max_bookings' => 3,
                    'is_active' => true,
                ]
            );
        }

        // Also assign specialized slots to Dhruvi and Dolly as examples
        $dhruvi = Specialist::where('name', 'Dhruvi Jyani')->first();
        if ($dhruvi) {
            TimeSlot::firstOrCreate(
                [
                    'specialist_id' => $dhruvi->id,
                    'period' => 'Morning',
                    'start_time' => '10:00 AM',
                    'end_time' => '11:30 AM',
                ],
                ['max_bookings' => 1, 'is_active' => true]
            );
        }

        // 6. Plans
        Plan::firstOrCreate(
            ['title' => 'Glow 3-Visit Plan'],
            [
                'tag' => 'First plan',
                'price' => 3299,
                'price_subtext' => '₹1,100 per visit',
                'description' => 'Three LumiSpa Glow Facials, spaced to suit your skin. Valid 90 days. Save ₹298.',
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        Plan::firstOrCreate(
            ['title' => 'Memberships and family plans'],
            [
                'tag' => 'Coming soon',
                'price' => 0,
                'price_subtext' => 'Tailored packages',
                'description' => 'Six-visit memberships, monthly plans and bundles for mothers, daughters and friends.',
                'is_featured' => false,
                'sort_order' => 2,
            ]
        );

        // 7. Devices
        $devices = [
            [
                'name' => 'LumiSpa iO',
                'pill' => 'Premium',
                'image' => 'images/serenu-professional-lumispa-io-system-silo-image.jpg',
                'description' => 'Our signature Glow Facial device, used with the Serenu Professional LumiSpa Cleanser.',
                'features' => [
                    'Micropulse oscillation with soft silicone heads for a thorough yet gentle cleanse',
                    'Cleanser with HydraShield Complex helps hydrate and support the moisture barrier',
                    'Made for face, neck and décolleté. Waterproof, with up to 50 sessions per charge',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Galvanic Spa',
                'pill' => 'Premium',
                'image' => 'images/serenu-professional-galvanic-spa-device.png',
                'description' => 'A microcurrent facial device used with Brightening Serum in our Radiance Ritual.',
                'features' => [
                    'Supports your skin’s natural radiance and a more even-looking tone',
                    'Helps reduce the appearance of fine lines',
                    'Helps increase hydration and visible firmness',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Prysm iO',
                'pill' => 'Premium',
                'image' => 'images/prysm-io-device-silo.png',
                'description' => 'A quick, painless fingertip scan of antioxidant levels in your skin.',
                'features' => [
                    'Results in about 15 seconds, with an AI-powered algorithm',
                    'Your Prysm Score runs from 0 to 600+, and higher is better',
                    'Track how diet, supplements and lifestyle change your score over time',
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($devices as $d) {
            Device::firstOrCreate(['name' => $d['name']], $d);
        }

        // 8. Products
        $products = [
            ['step_name' => 'CLEANSE', 'name' => 'Face Wash', 'image' => 'images/glow.png', 'category' => 'daily', 'sort_order' => 1],
            ['step_name' => 'TONE', 'name' => 'Glow Toner', 'image' => 'images/Nu-Ski-Glow.jpg', 'category' => 'daily', 'sort_order' => 2],
            ['step_name' => 'TREAT', 'name' => 'Vitamin C Serum', 'image' => 'images/products/vitamin-c-serum.jpg', 'category' => 'daily', 'sort_order' => 3],
            ['step_name' => 'MOISTURISE', 'name' => 'Light Moisturizer', 'image' => 'images/products/light-moisturizer.jpg', 'category' => 'daily', 'sort_order' => 4],
            ['step_name' => 'PROTECT', 'name' => 'SunRight SPF 50+', 'image' => 'images/products/sunright-spf50.jpg', 'category' => 'daily', 'sort_order' => 5],
            ['step_name' => 'DEVICE', 'name' => 'LumiSpa iO for home', 'image' => 'images/serenu-professional-lumispa-io-system-silo-image.jpg', 'category' => 'daily', 'sort_order' => 6],
            ['step_name' => null, 'name' => 'Pharmanex LifePak Elements', 'image' => null, 'category' => 'wellness', 'description' => 'Daily vitamins and minerals designed for common Indian dietary gaps.', 'sort_order' => 7],
            ['step_name' => null, 'name' => 'Pharmanex Optimum Omega', 'image' => null, 'category' => 'wellness', 'description' => 'Omega-3 from fish oil as part of a balanced diet.', 'sort_order' => 8],
            ['step_name' => null, 'name' => 'TRMe weight wellness', 'image' => null, 'category' => 'wellness', 'description' => 'A balanced eating and lifestyle programme. Coming soon.', 'sort_order' => 9],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['name' => $p['name']], $p);
        }

        // 9. Concerns
        $concerns = [
            ['quote' => '“My skin looks dull and tired”', 'solution' => 'Try the Nu Skin LumiSpa Glow Facial for a fresh, bright look.', 'link_text' => 'See the facial →', 'link_target' => '#services', 'is_dark' => false, 'sort_order' => 1],
            ['quote' => '“I want a gentle first step”', 'solution' => 'Start with the FMZ Glow Cleanse: simple cleansing, hydration and advice.', 'link_text' => 'See the cleanse →', 'link_target' => '#services', 'is_dark' => false, 'sort_order' => 2],
            ['quote' => '“I deserve a proper pamper”', 'solution' => 'The Nu Skin Radiance Ritual is our most complete experience.', 'link_text' => 'See the ritual →', 'link_target' => '#services', 'is_dark' => false, 'sort_order' => 3],
            ['quote' => '“I have an event coming up”', 'solution' => 'Event-ready skincare is coming soon. Ask us what works best for now.', 'link_text' => 'Ask us →', 'link_target' => '#expert', 'is_dark' => false, 'sort_order' => 4],
            ['quote' => '“I need a routine I can keep”', 'solution' => 'Get a simple daily routine with products chosen for your skin.', 'link_text' => 'Build my routine →', 'link_target' => '#products', 'is_dark' => false, 'sort_order' => 5],
            ['quote' => '“I’m not sure what my skin needs”', 'solution' => 'That’s completely fine. Chat with us first, free, no obligation.', 'link_text' => 'Get guidance →', 'link_target' => '#expert', 'is_dark' => true, 'sort_order' => 6],
        ];

        foreach ($concerns as $c) {
            Concern::firstOrCreate(['quote' => $c['quote']], $c);
        }

        // 10. Reviews & Results
        $reviews = [
            [
                'client_name' => 'Priya, 28',
                'service_tag' => 'Nu Skin LumiSpa Glow Facial',
                'concern' => 'Dull, tired skin',
                'plan_info' => '3 visits over 12 weeks',
                'products_used' => 'LumiSpa iO, Face Wash, Vitamin C Serum, SunRight SPF 50+',
                'quote' => '“My skin looks fresher and my makeup sits better.”',
                'image_after' => 'images/priya.jpg',
                'is_featured_result' => true,
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Neha, 34',
                'service_tag' => 'FMZ Glow Cleanse',
                'concern' => 'Dry, tight skin',
                'plan_info' => '2 visits over 4 weeks',
                'products_used' => 'Face Wash, Glow Toner, Light Moisturizer',
                'quote' => '“It feels soft and comfortable all day.”',
                'image_after' => 'images/neha.jpg',
                'is_featured_result' => true,
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Mansi Joshi',
                'service_tag' => 'LumiSpa Facial Experience',
                'quote' => '“I had a lovely experience at FMZ Glow! The facial was so relaxing, and my skin felt clean, soft and refreshed afterwards. The specialist was very friendly, and I felt comfortable throughout the session”',
                'is_featured_result' => false,
                'sort_order' => 3,
            ],
            [
                'client_name' => 'Hetal Patel',
                'service_tag' => 'Prysm Scan',
                'quote' => '“The antioxidant scan was something new for me. I found it really interesting to learn more about my antioxidant levels and understand my wellness better.”',
                'is_featured_result' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($reviews as $r) {
            Review::firstOrCreate(['client_name' => $r['client_name']], $r);
        }

        // 11. FAQs
        $faqs = [
            ['question' => 'Is the facial suitable for my skin type?', 'answer' => 'Our services are personalised to your skin\'s needs. Before beginning, your specialist will discuss your skin concerns and check whether the selected products and treatment are suitable for you.'],
            ['question' => 'Can I get a facial if I have sensitive skin or acne?', 'answer' => 'Please tell us about any sensitivity, acne, allergies or ongoing skin concerns when booking. Your specialist will assess product suitability, and we may recommend postponing the service or consulting a dermatologist.'],
            ['question' => 'Can I get a facial during pregnancy or while breastfeeding?', 'answer' => 'Some skincare products and device treatments may not be suitable during pregnancy or breastfeeding. Please inform us when booking so we can discuss appropriate options and any necessary medical advice.'],
            ['question' => 'Will I experience redness, irritation or breakouts after the facial?', 'answer' => 'Skin reactions vary from person to person. Although we use products according to their instructions and check suitability beforehand, temporary redness or irritation may occur. Please contact us if you experience a persistent or unexpected reaction.'],
            ['question' => 'How long does a facial appointment take?', 'answer' => 'Appointment duration depends on the service selected. We will confirm the expected duration when your booking is confirmed.'],
            ['question' => 'What do I need to prepare before the specialist arrives?', 'answer' => 'Just a comfortable, well-lit space where you can relax. Your specialist brings the products, devices and essentials required for your booked service.'],
            ['question' => 'Will the same specialist visit me every time?', 'answer' => 'We will do our best to assign your preferred specialist for repeat visits, subject to availability.'],
            ['question' => 'Can I book a facial for my mother, friend or family member?', 'answer' => 'Yes. You can enquire about booking a service for someone else. Please share their skin concerns and preferred appointment details with us in advance.'],
            ['question' => 'Do I have to purchase Nu Skin products after my facial?', 'answer' => 'No. Product purchases are entirely optional. Your specialist will explain your skincare options, but there is no obligation to buy anything.'],
            ['question' => 'Are there any additional or travel charges?', 'answer' => 'Any applicable travel charges or additional costs will be communicated to you in writing before your appointment is confirmed.'],
            ['question' => 'Can I use my own skincare products during the facial?', 'answer' => 'Our services are designed around selected Nu Skin products and approved device protocols. Please let us know if you have a preferred product or ingredient concern before your appointment.'],
            ['question' => 'How soon will I see results, and how long will they last?', 'answer' => 'Results vary depending on your skin, the service and your home-care routine. Some people notice an immediate refreshed feeling, while longer-term cosmetic improvements may require consistent care. We cannot guarantee specific results.'],
            ['question' => 'What is the difference between LumiSpa iO and Galvanic Spa?', 'answer' => 'LumiSpa iO is used for device-assisted cleansing, while Galvanic Spa uses microcurrent technology as part of selected skincare treatments. Your specialist can explain which service is appropriate for your needs.'],
            ['question' => 'Is the Prysm iO scan a medical test?', 'answer' => 'No. Prysm iO provides a cosmetic wellness-related antioxidant score. It is not a medical test, does not diagnose disease and should not replace medical advice.'],
            ['question' => 'Will I receive a personalised skincare routine after my visit?', 'answer' => 'Yes. Your specialist will share practical skincare guidance and explain how to use recommended products at home. Purchasing those products is optional.'],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::firstOrCreate(['question' => $faq['question']], [
                'answer' => $faq['answer'],
                'sort_order' => $i + 1,
                'is_active' => true,
            ]);
        }

        // 12. Email Templates
        $emailTemplates = [
            [
                'code' => 'booking_received',
                'name' => 'Customer: Booking Request Received',
                'subject' => 'We received your FMZ Glow booking request (#{booking_number})',
                'content' => "Hello {name},\n\nThank you for choosing FMZ Glow! We have received your booking request.\n\nBooking Details:\n- Booking ID: #{booking_number}\n- Service: {service}\n- Preferred Date: {date}\n- Preferred Time: {time}\n- Pincode: {pincode}\n- Skin Concern: {skin_concern}\n\nOur team will contact you on WhatsApp ({phone}) shortly to confirm your certified specialist, exact appointment time, and total price.\n\nWarm regards,\nFMZ Glow Team\nWhatsApp: {whatsapp_number}",
                'variables_hint' => '{name}, {booking_number}, {service}, {date}, {time}, {pincode}, {phone}, {skin_concern}, {whatsapp_number}',
            ],
            [
                'code' => 'booking_confirmed',
                'name' => 'Customer: Booking Confirmed & Specialist Assigned',
                'subject' => 'Confirmed: Your FMZ Glow visit on {date}',
                'content' => "Hello {name},\n\nYour FMZ Glow at-home skincare visit is officially confirmed!\n\nAppointment Details:\n- Booking ID: #{booking_number}\n- Specialist: {specialist}\n- Service: {service}\n- Date: {date}\n- Time Slot: {time}\n- Total Price: ₹{total_amount}\n\nWhat to prepare:\nA comfortable, well-lit space. Your specialist will bring all genuine Nu Skin devices and sealed products.\n\nRemember: Payment is made after your visit via UPI or Cash.\n\nWarm regards,\nFMZ Glow Team",
                'variables_hint' => '{name}, {booking_number}, {specialist}, {service}, {date}, {time}, {total_amount}',
            ],
            [
                'code' => 'admin_new_booking',
                'name' => 'Admin: New Booking Alert',
                'subject' => '🔔 New FMZ Glow Booking (#{booking_number}) - {name}',
                'content' => "A new booking has just been submitted on FMZ Glow.\n\nDetails:\n- Booking ID: #{booking_number}\n- Customer Name: {name}\n- WhatsApp: {phone}\n- Pincode: {pincode}\n- Service: {service}\n- Preferred Date: {date}\n- Preferred Time Slot: {time}\n- Skin Concern: {skin_concern}\n\nPlease review this booking in the admin panel and assign a specialist.",
                'variables_hint' => '{name}, {booking_number}, {phone}, {pincode}, {service}, {date}, {time}, {skin_concern}',
            ],
            [
                'code' => 'payment_received',
                'name' => 'Customer: Payment Receipt',
                'subject' => 'Payment Received for FMZ Glow Booking #{booking_number}',
                'content' => "Hello {name},\n\nWe have received your payment of ₹{total_amount} for booking #{booking_number}.\n\nPayment Details:\n- Payment ID: {payment_id}\n- Service: {service}\n- Date: {date}\n- Status: Paid\n\nThank you for experiencing the glow with FMZ India!\n\nWarm regards,\nFMZ Glow Team",
                'variables_hint' => '{name}, {booking_number}, {total_amount}, {payment_id}, {service}, {date}',
            ],
        ];

        foreach ($emailTemplates as $tmpl) {
            EmailTemplate::firstOrCreate(['code' => $tmpl['code']], $tmpl);
        }

        // 13. Customer Bookings for testing
        $this->call(FakeBookingsSeeder::class);
    }
}
