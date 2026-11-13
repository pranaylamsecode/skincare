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
            'active_payment_gateway' => 'razorpay',
            'razorpay_key' => 'rzp_test_placeholder',
            'razorpay_secret' => 'rzp_secret_placeholder',
            'razorpay_enabled' => '0',
            'zoho_enabled' => '0',
            'zoho_client_id' => '',
            'zoho_client_secret' => '',
            'zoho_checkout_url' => '',
            'zoho_merchant_id' => '',
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
                'description' => 'Deep cleansing and hydration with the Nu Skin LumiSpa iO.',
                'features' => [
                    'Skin suitability check',
                    'LumiSpa iO cleanse',
                    'Moisturising finish',
                    'Simple aftercare tips',
                ],
                'price' => 799,
                'original_price' => null,
                'launch_label' => null,
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=900&q=80',
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'title' => 'FMZ Glow Facial',
                'slug' => 'fmz-glow-facial',
                'tag' => 'Launch offer',
                'tag_style' => 'tan',
                'description' => 'Our signature facial with the Nu Skin Galvanic Spa and selected Nu Skin products.',
                'features' => [
                    'Skin consultation',
                    'Galvanic Spa facial treatment',
                    'Leaves skin soft and refreshed',
                    'Home-care tips',
                ],
                'price' => 999,
                'original_price' => 1199,
                'launch_label' => 'launch price',
                'image' => 'images/fmz-glow-facial.png',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'FMZ Radiance Ritual',
                'slug' => 'fmz-radiance-ritual',
                'tag' => 'Most complete',
                'tag_style' => 'dark',
                'description' => 'The Glow Facial plus a 15-second Prysm iO antioxidant scan, explained by your specialist.',
                'features' => [
                    'Extended consultation',
                    'Galvanic Spa facial',
                    'Prysm iO antioxidant score',
                    'Personal skincare and food tips',
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
            Service::updateOrCreate(['slug' => $svc['slug']], $svc);
        }

        // 4. Specialists
        $specialists = [
            [
                'name' => 'Dhruvi Jyani',
                'qualification' => 'B.Sc. in Biochemistry',
                'location' => 'Paldi & Central Ahmedabad (380007)',
                'pincode' => '380007',
                'serviceable_pincodes' => '380007, 380006, 380001',
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
                'serviceable_pincodes' => '382415, 382350, 380026',
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
                'serviceable_pincodes' => '382418, 382449',
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
                'serviceable_pincodes' => '380009, 380015, 380054',
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
                'serviceable_pincodes' => '380009, 380015, 380052',
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
                'serviceable_pincodes' => '382421, 382007',
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
                'serviceable_pincodes' => '395006, 395007',
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
                'serviceable_pincodes' => '360004, 360001',
                'city' => 'rajkot',
                'languages' => ['Gujarati', 'Hindi', 'English'],
                'photo' => 'images/Diya.png',
                'badge' => 'Certified Care Specialist',
                'sort_order' => 8,
            ],
        ];

        foreach ($specialists as $spec) {
            $specialist = Specialist::updateOrCreate(['name' => $spec['name']], $spec);

            $staffEmail = strtolower(explode(' ', $spec['name'])[0]) . '@fmzglow.com';
            User::updateOrCreate(
                ['email' => $staffEmail],
                [
                    'name' => $spec['name'],
                    'password' => Hash::make('staff123'),
                    'role' => 'staff',
                    'specialist_id' => $specialist->id,
                    'email_verified_at' => now(),
                ]
            );
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
        Plan::updateOrCreate(
            ['title' => 'Glow 3-Visit Plan'],
            [
                'tag' => 'Launch plan',
                'price' => 2799,
                'price_subtext' => '₹933 per visit',
                'description' => 'Three Glow Facials with the same specialist, spaced to suit your skin. Valid 90 days.',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
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

        // 9. Concerns (Exactly 4 cards matching client reference design)
        $concerns = [
            ['quote' => '“My skin looks dull and tired”', 'solution' => 'The Glow Facial brings back a fresh, bright look.', 'link_text' => 'Book the Glow Facial', 'link_target' => '#book', 'is_dark' => false, 'sort_order' => 1],
            ['quote' => '“I want a gentle first step”', 'solution' => 'Start with the Glow Cleanse: cleansing, hydration and advice.', 'link_text' => 'Book the Glow Cleanse', 'link_target' => '#book', 'is_dark' => false, 'sort_order' => 2],
            ['quote' => '“I deserve a proper pamper”', 'solution' => 'The Radiance Ritual is our most complete visit.', 'link_text' => 'Book the Radiance Ritual', 'link_target' => '#book', 'is_dark' => false, 'sort_order' => 3],
            ['quote' => '“I’m not sure what my skin needs”', 'solution' => 'Chat with us first. It’s free, with no obligation.', 'link_text' => 'Chat on WhatsApp', 'link_target' => 'whatsapp', 'is_dark' => true, 'sort_order' => 4],
        ];

        Concern::where('quote', 'like', '%event%')->orWhere('quote', 'like', '%routine%')->delete();
        foreach ($concerns as $c) {
            Concern::updateOrCreate(['quote' => $c['quote']], $c);
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

        // 13. Pincodes database for radius auto-assignment
        $this->call(PincodeSeeder::class);

        // 14. Customer Bookings for testing
        $this->call(FakeBookingsSeeder::class);
    }
}
