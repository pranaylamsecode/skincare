<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'meta_description' => 'Privacy Policy for FMZ Glow at-home skincare services. How we protect your data and personal information.',
                'sort_order' => 1,
                'content' => "### 1. Introduction
Welcome to FMZ Glow (\"FMZ India\", \"we\", \"our\", or \"us\"). We provide certified at-home skincare and wellness services delivered by trained women specialists in Ahmedabad, Gandhinagar, Surat, Rajkot, and surrounding areas. We respect your privacy and are committed to protecting your personal data.

### 2. Information We Collect
We collect only the information necessary to provide, confirm, and deliver our at-home skincare visits safely:
- **Contact Details:** Your full name, mobile / WhatsApp number, and email address.
- **Visit Address:** Your residential address, locality, and postal pincode so our certified specialist can navigate to your home.
- **Skin & Health Information:** Optional skin concerns, allergies, or conditions shared during booking to help your specialist prepare the right Nu Skin products and ensure treatment safety (e.g., pregnancy or medical implants for Galvanic Spa).
- **Communication History:** Booking requests, confirmations, and customer support messages exchanged via WhatsApp or email.

### 3. How We Use Your Information
Your information is used exclusively for:
- Scheduling appointments and assigning a verified woman specialist to your home.
- Sending written booking confirmations, specialist identity, exact arrival time, and total price via WhatsApp and email.
- Calculating travel distance (free within 8 km; transparent nominal travel confirmed in writing for farther distances).
- Providing aftercare tips and voluntary customer service follow-ups.

### 4. Specialist Verification & Confidentiality
All FMZ Glow specialists are verified women professionals with science, pharmacy, or nursing backgrounds. Your residential address and phone number are shared solely with the assigned specialist for the purpose of completing your visit. We never sell, rent, or trade your personal information to third-party marketers or advertisers.

### 5. Secure Payments
We offer **Pay After Your Visit** via UPI or Cash directly to your specialist once you are fully satisfied with your treatment. For customers choosing to pay online, payment transactions are encrypted and processed through RBI-approved gateways (Razorpay / Zoho Payments). We do not store your credit card numbers, debit card details, or UPI PINs.

### 6. Data Security & Retention
We maintain administrative and technical safeguards to keep your personal data secure. Booking records are retained only as long as necessary to satisfy operational, accounting, and legal requirements.

### 7. Contact Us
If you have any questions about this Privacy Policy or wish to access, correct, or delete your personal details, please contact us:
- **WhatsApp / Phone:** +91 73037 06047
- **Email:** fmz@fmzau.com.au
- **Operating Hours:** 9:00 AM – 8:00 PM (Monday – Sunday)",
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-and-conditions',
                'meta_description' => 'Terms and Conditions for FMZ Glow at-home skincare bookings and appointments.',
                'sort_order' => 2,
                'content' => "### 1. Overview & Agreement
By booking an appointment through FMZ Glow (myfmz.com), WhatsApp, or telephone, you agree to these Terms and Conditions. Please review them carefully before scheduling a visit.

### 2. Women Specialists Guarantee
- **Exclusively Certified Women Specialists:** All at-home treatments are performed exclusively by certified women specialists with pharmacy, nursing, or science credentials.
- **Zero Tolerance Policy:** We maintain a strict zero-tolerance policy against any inappropriate behavior, harassment, or unsafe conditions directed toward our specialists. The specialist reserves the unconditional right to leave immediately if safety or dignity is compromised, and legal action may be pursued where warranted.

### 3. Genuine Nu Skin Products & Safety Protocols
- **Authentic & Sealed:** We exclusively use authentic, genuine Nu Skin devices (LumiSpa iO, Galvanic Spa, Prysm iO) and professional skincare formulations. Products are unsealed in front of you.
- **Pre-Visit Skin & Health Consultation:** Every visit begins with a brief consultation. Before using the Galvanic Spa, your specialist will verify contraindications (such as pregnancy, pacemakers, metal dental/facial implants, or acute skin infections). If contraindicated, she will skip or adapt that step for your safety.

### 4. Appointment Booking & Confirmation
- **Written Confirmation on WhatsApp:** Once you submit a booking request, our coordination team confirms your specialist's name, phone number, confirmed date, time slot, and total price in writing on WhatsApp within 30 minutes during operating hours (9:00 AM to 8:00 PM).
- **Service Availability:** Services are offered across Ahmedabad, Gandhinagar, Surat, and Rajkot within serviceable pincodes.

### 5. Pricing, Travel & Payment
- **Transparent Pricing:** The price confirmed in writing is the final price. There are no hidden setup fees or surprise charges.
- **Travel Policy:** Travel is completely **free within 8 km** of the assigned specialist. For locations beyond 8 km, any modest travel surcharge will be calculated transparently and confirmed with you in writing before dispatching the specialist.
- **Pay After Your Visit:** You may pay by UPI or Cash after your visit once you are satisfied with your treatment. If you choose online prepayment, receipts are delivered electronically to your email.

### 6. Modifications to Service
FMZ Glow reserves the right to modify service offerings, pricing, or terms with prior notice published on this website. Confirmed bookings will always be honored at the price confirmed at the time of scheduling.",
            ],
            [
                'title' => 'Cancellation and Refunds',
                'slug' => 'cancellation-and-refunds',
                'meta_description' => 'Cancellation and refund policies for FMZ Glow appointments and multi-visit plans.',
                'sort_order' => 3,
                'content' => "### 1. Free Cancellation & Rescheduling
We understand that plans can change. We offer complete flexibility:
- **Free Cancellation:** You can cancel or reschedule your appointment with **no fee or penalty** up to **4 hours** before your scheduled appointment time.
- **How to Reschedule or Cancel:** Simply message our team on WhatsApp at **+91 73037 06047** or call us with your booking ID.

### 2. Late Cancellations & Doorstep No-Shows
- If an appointment is cancelled with less than 2 hours' notice after the specialist has already commenced travel or arrived at your doorstep, a nominal travel convenience fee of **₹150** may be requested to compensate the specialist for her travel time.
- If you are unavailable or unresponsive at the scheduled address for more than 20 minutes past the scheduled time slot, the visit may be marked as a no-show.

### 3. Refund Policy for Online Prepayments
- If you prepaid online and cancel at least 4 hours before the appointment, a **100% full refund** will be automatically initiated to your original payment method (Bank Account, UPI, or Credit/Debit Card).
- Refunds typically reflect in your account within **5 to 7 business days**, depending on your issuing bank.

### 4. Multi-Visit Plans (e.g. Glow 3-Visit Plan)
- **Validity:** The Glow 3-Visit Plan (₹2,799 / ₹933 per visit) is valid for **90 days** from the date of the first visit.
- **Transferability:** Visits can be shared with immediate family members (e.g., mother, sister, or daughter) residing at the same address.
- **Plan Cancellation & Pro-Rata Refunds:** If you wish to discontinue a 3-visit plan after completing 1 visit, the completed session will be billed at the standard individual visit rate (₹999), and the entire remaining balance (₹1,800) will be promptly refunded to you.

### 5. Satisfaction Guarantee
Your comfort, safety, and glow are our highest priorities. If you are not completely satisfied with your specialist's service or if any product causes unexpected discomfort, please alert us within 24 hours of your visit. Our care coordinator will arrange a complimentary follow-up or provide an appropriate resolution.

### 6. Support Contact
For all cancellation, rescheduling, and refund requests:
- **WhatsApp Support:** +91 73037 06047
- **Email:** fmz@fmzau.com.au
- **Response Time:** Typically within 30 minutes (9:00 AM – 8:00 PM)",
            ],
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }
    }
}
