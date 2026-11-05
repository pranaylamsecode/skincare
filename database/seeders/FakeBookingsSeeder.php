<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FakeBookingsSeeder extends Seeder
{
    public function run(): void
    {
        $galvanic = Service::where('slug', 'fmz-galvanic-glow-facial')->first();
        $cleanse = Service::where('slug', 'fmz-glow-cleanse')->first();
        $ritual = Service::where('slug', 'fmz-prysm-io-radiance-ritual')->first();

        $dhruvi = Specialist::where('name', 'like', '%Dhruvi%')->first();
        $diya = Specialist::where('name', 'like', '%Diya%')->first();
        $dolly = Specialist::where('name', 'like', '%Dolly%')->first();

        $morningSlot = TimeSlot::where('period', 'Morning')->first();
        $afternoonSlot = TimeSlot::where('period', 'Afternoon')->first();
        $eveningSlot = TimeSlot::where('period', 'Evening')->first();

        $fakeBookings = [
            // Customer 1: Pooja Sharma (Phone: 9876543210)
            [
                'booking_number' => 'FMZ-20261106-9A1F',
                'name' => 'Pooja Sharma',
                'phone' => '9876543210',
                'email' => 'pooja.sharma@example.com',
                'pincode' => '380015',
                'service_id' => $galvanic?->id,
                'service_name' => $galvanic ? $galvanic->title : 'FMZ Galvanic Glow Facial',
                'specialist_id' => $dhruvi?->id,
                'specialist_name' => $dhruvi ? $dhruvi->name : 'Dhruvi Jyani',
                'booking_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time_slot_id' => $morningSlot?->id,
                'time_slot_text' => 'Morning (10:00 AM - 11:30 AM)',
                'skin_concern' => 'Dull skin and tired appearance after long workweek | Address: Flat 402, Shivalik Park, Vastrapur',
                'status' => 'confirmed',
                'total_amount' => 999.00,
                'payment_status' => 'unpaid',
                'payment_method' => 'after_visit',
                'admin_notes' => 'Confirmed with client on WhatsApp. Specialist informed with kit.',
                'consent' => true,
            ],
            [
                'booking_number' => 'FMZ-20261028-4K2P',
                'name' => 'Pooja Sharma',
                'phone' => '9876543210',
                'email' => 'pooja.sharma@example.com',
                'pincode' => '380015',
                'service_id' => $cleanse?->id,
                'service_name' => $cleanse ? $cleanse->title : 'FMZ Glow Cleanse',
                'specialist_id' => $diya?->id,
                'specialist_name' => $diya ? $diya->name : 'Diya Patel',
                'booking_date' => Carbon::now()->subDays(8)->format('Y-m-d'),
                'time_slot_id' => $afternoonSlot?->id,
                'time_slot_text' => 'Afternoon (02:00 PM - 03:30 PM)',
                'skin_concern' => 'General cleansing and hydration | Vastrapur',
                'status' => 'completed',
                'total_amount' => 799.00,
                'payment_status' => 'paid',
                'payment_method' => 'after_visit',
                'admin_notes' => 'Service completed smoothly. Client rated 5 stars.',
                'consent' => true,
            ],
            [
                'booking_number' => 'FMZ-20261112-7L9Q',
                'name' => 'Pooja Sharma',
                'phone' => '9876543210',
                'email' => 'pooja.sharma@example.com',
                'pincode' => '380015',
                'service_id' => $ritual?->id,
                'service_name' => $ritual ? $ritual->title : 'FMZ Prysm iO Radiance Ritual',
                'specialist_id' => $dhruvi?->id,
                'specialist_name' => $dhruvi ? $dhruvi->name : 'Dhruvi Jyani',
                'booking_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'time_slot_id' => $eveningSlot?->id,
                'time_slot_text' => 'Evening (05:30 PM - 07:00 PM)',
                'skin_concern' => 'Pre-wedding skin glow and antioxidant scan | Vastrapur',
                'status' => 'pending',
                'total_amount' => 1699.00,
                'payment_status' => 'unpaid',
                'payment_method' => 'after_visit',
                'admin_notes' => 'Awaiting final slot confirmation from specialist.',
                'consent' => true,
            ],

            // Customer 2: Ananya Patel (Phone: 9898989898)
            [
                'booking_number' => 'FMZ-20261105-8B3X',
                'name' => 'Ananya Patel',
                'phone' => '9898989898',
                'email' => 'ananya.p@example.com',
                'pincode' => '380054',
                'service_id' => $galvanic?->id,
                'service_name' => $galvanic ? $galvanic->title : 'FMZ Galvanic Glow Facial',
                'specialist_id' => $diya?->id,
                'specialist_name' => $diya ? $diya->name : 'Diya Patel',
                'booking_date' => Carbon::now()->format('Y-m-d'),
                'time_slot_id' => $afternoonSlot?->id,
                'time_slot_text' => 'Afternoon (02:30 PM - 04:00 PM)',
                'skin_concern' => 'Fine lines around forehead and eyes | Address: Villa 12, Gulmohar Greens, Thaltej',
                'status' => 'in_progress',
                'total_amount' => 999.00,
                'payment_status' => 'unpaid',
                'payment_method' => 'after_visit',
                'admin_notes' => 'Specialist en route with LumiSpa & Galvanic devices.',
                'consent' => true,
            ],
            [
                'booking_number' => 'FMZ-20261019-3M7T',
                'name' => 'Ananya Patel',
                'phone' => '9898989898',
                'email' => 'ananya.p@example.com',
                'pincode' => '380054',
                'service_id' => $cleanse?->id,
                'service_name' => $cleanse ? $cleanse->title : 'FMZ Glow Cleanse',
                'specialist_id' => null,
                'specialist_name' => null,
                'booking_date' => Carbon::now()->subDays(17)->format('Y-m-d'),
                'time_slot_id' => $morningSlot?->id,
                'time_slot_text' => 'Morning (10:00 AM - 11:30 AM)',
                'skin_concern' => 'Dehydrated skin',
                'status' => 'cancelled',
                'total_amount' => 799.00,
                'payment_status' => 'unpaid',
                'payment_method' => 'after_visit',
                'admin_notes' => 'Cancelled by customer: Client had emergency out-of-town travel.',
                'consent' => true,
            ],

            // Customer 3: Ritu Desai (Phone: 7303706047)
            [
                'booking_number' => 'FMZ-20261108-5W8E',
                'name' => 'Ritu Desai',
                'phone' => '+917303706047',
                'email' => 'ritu.desai@example.com',
                'pincode' => '380009',
                'service_id' => $galvanic?->id,
                'service_name' => '3-Visit Glow Transformation Pack',
                'specialist_id' => $dolly?->id,
                'specialist_name' => $dolly ? $dolly->name : 'Dolly Christian',
                'booking_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time_slot_id' => $morningSlot?->id,
                'time_slot_text' => 'Morning (11:30 AM - 01:00 PM)',
                'skin_concern' => '3-Visit pack session 1 of 3 | Address: 201, Orchid Tower, Navrangpura',
                'status' => 'confirmed',
                'total_amount' => 2400.00,
                'payment_status' => 'unpaid',
                'payment_method' => 'after_visit',
                'admin_notes' => 'Client booked package deal. Assigned senior specialist Dolly.',
                'consent' => true,
            ],
            [
                'booking_number' => 'FMZ-20261012-1V4R',
                'name' => 'Ritu Desai',
                'phone' => '+917303706047',
                'email' => 'ritu.desai@example.com',
                'pincode' => '380009',
                'service_id' => $galvanic?->id,
                'service_name' => $galvanic ? $galvanic->title : 'FMZ Galvanic Glow Facial',
                'specialist_id' => $dhruvi?->id,
                'specialist_name' => $dhruvi ? $dhruvi->name : 'Dhruvi Jyani',
                'booking_date' => Carbon::now()->subDays(24)->format('Y-m-d'),
                'time_slot_id' => $afternoonSlot?->id,
                'time_slot_text' => 'Afternoon (03:30 PM - 05:00 PM)',
                'skin_concern' => 'Trial session before purchasing package | Navrangpura',
                'status' => 'completed',
                'total_amount' => 999.00,
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
                'admin_notes' => 'Paid online via UPI. Very happy with Dhruvi.',
                'consent' => true,
            ],

            // Customer 4: Meera Joshi (Phone: 9123456780)
            [
                'booking_number' => 'FMZ-20261107-2P9Z',
                'name' => 'Meera Joshi',
                'phone' => '9123456780',
                'email' => 'meera.joshi@example.com',
                'pincode' => '380007',
                'service_id' => $galvanic?->id,
                'service_name' => $galvanic ? $galvanic->title : 'FMZ Galvanic Glow Facial',
                'specialist_id' => $dhruvi?->id,
                'specialist_name' => $dhruvi ? $dhruvi->name : 'Dhruvi Jyani',
                'booking_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time_slot_id' => $morningSlot?->id,
                'time_slot_text' => 'Morning (10:00 AM - 11:30 AM)',
                'skin_concern' => 'Sensitive skin prone to seasonal redness | Address: Bungalow 4, Sharda Society, Paldi',
                'status' => 'confirmed',
                'total_amount' => 999.00,
                'payment_status' => 'unpaid',
                'payment_method' => 'after_visit',
                'admin_notes' => 'Paldi resident. specialist Dhruvi assigned due to proximity.',
                'consent' => true,
            ],
        ];

        foreach ($fakeBookings as $b) {
            Booking::updateOrCreate(
                ['booking_number' => $b['booking_number']],
                $b
            );
        }
    }
}

