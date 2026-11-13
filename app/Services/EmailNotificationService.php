<?php

namespace App\Services;

use App\Mail\TemplateMail;
use App\Models\Booking;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Send email based on dynamic template code
     */
    public static function send(string $templateCode, string $recipientEmail, array $variables): bool
    {
        try {
            $template = EmailTemplate::where('code', $templateCode)->where('is_active', true)->first();
            if (!$template) {
                Log::warning("Email template '{$templateCode}' not found or inactive.");
                return false;
            }

            // Merge site settings variables
            $siteLogo = Setting::get('site_logo', 'images/FMZ Glow logo.jpg');
            $logoUrl = filter_var($siteLogo, FILTER_VALIDATE_URL) ? $siteLogo : url('/' . ltrim($siteLogo, '/'));
            $variables['site_name'] = Setting::get('site_name', 'FMZ Glow');
            $variables['whatsapp_number'] = Setting::get('whatsapp_number', '+917303706047');
            $variables['site_logo'] = $logoUrl;
            $variables['logo_url'] = $logoUrl;

            $rendered = $template->render($variables);

            Mail::to($recipientEmail)->send(new TemplateMail($rendered['subject'], $rendered['content'], $logoUrl));

            Log::info("Email '{$templateCode}' successfully sent to {$recipientEmail}.");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send email '{$templateCode}' to {$recipientEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send booking received notifications to customer and admin
     */
    public static function notifyBookingCreated(Booking $booking): void
    {
        $vars = [
            'name' => $booking->name,
            'booking_number' => $booking->booking_number,
            'phone' => $booking->phone,
            'pincode' => $booking->pincode,
            'service' => $booking->service_name,
            'date' => $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A',
            'time' => $booking->time_slot_text ?? 'No preference',
            'specialist' => $booking->specialist_name ?? 'Assigned soon',
            'skin_concern' => $booking->skin_concern ?: 'None mentioned',
            'total_amount' => number_format((float) $booking->total_amount, 2),
        ];

        // 1. Send confirmation email to client if email provided
        if (!empty($booking->email)) {
            self::send('booking_confirmed', $booking->email, $vars);
        }

        // 2. Notify business owner / admin
        $adminEmail = Setting::get('business_email', 'fmz@fmzau.com.au');
        if (!empty($adminEmail)) {
            self::send('admin_new_booking', $adminEmail, $vars);
        }
    }

    /**
     * Notify customer when booking is confirmed by admin
     */
    public static function notifyBookingConfirmed(Booking $booking): void
    {
        if (empty($booking->email)) {
            return;
        }

        $vars = [
            'name' => $booking->name,
            'booking_number' => $booking->booking_number,
            'phone' => $booking->phone,
            'service' => $booking->service_name,
            'date' => $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A',
            'time' => $booking->time_slot_text ?? 'Confirmed time',
            'specialist' => $booking->specialist_name ?? 'FMZ Specialist',
            'total_amount' => number_format((float) $booking->total_amount, 2),
        ];

        self::send('booking_confirmed', $booking->email, $vars);
    }

    /**
     * Notify customer on payment receipt
     */
    public static function notifyPaymentReceived(Booking $booking): void
    {
        if (empty($booking->email)) {
            return;
        }

        $vars = [
            'name' => $booking->name,
            'booking_number' => $booking->booking_number,
            'total_amount' => number_format((float) $booking->total_amount, 2),
            'payment_id' => $booking->razorpay_payment_id ?? 'Direct UPI/Cash',
            'service' => $booking->service_name,
            'date' => $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A',
        ];

        self::send('payment_received', $booking->email, $vars);
    }

    /**
     * Send thank you confirmation email to CME applicant and alert to admin
     */
    public static function notifyCmeRegistered($cme): void
    {
        $programmesList = is_array($cme->programmes) ? implode(', ', $cme->programmes) : (string)$cme->programmes;
        $fullName = trim(($cme->first_name ?? '') . ' ' . ($cme->last_name ?? ''));
        $regId = $cme->registration_id ?? 'FMZ-CME';

        $vars = [
            'name' => $fullName ?: 'Applicant',
            'first_name' => $cme->first_name ?? 'Applicant',
            'registration_id' => $regId,
            'programmes' => $programmesList ?: 'Beauty & Wellness',
            'city' => $cme->city ?? '',
            'state' => $cme->state ?? 'Gujarat',
            'pincode' => $cme->pincode ?? '',
            'mobile' => $cme->mobile ?? '',
            'email' => $cme->email ?? '',
            'highest_qualification' => $cme->highest_qualification ?? 'N/A',
            'vehicle' => $cme->vehicle ?? 'N/A',
            'driving_licence' => $cme->driving_licence ?? 'N/A',
            'referral_source' => $cme->referral_source ?? 'Direct',
        ];

        // 1. Send Thank You email to applicant if email is provided
        if (!empty($cme->email)) {
            $sent = self::send('cme_registered', $cme->email, $vars);
            // Fallback direct branded email if template not seeded
            if (!$sent) {
                try {
                    $whatsappNum = Setting::get('whatsapp_number', '+917303706047');
                    $siteLogo = Setting::get('site_logo', 'images/FMZ Glow logo.jpg');
                    $logoUrl = filter_var($siteLogo, FILTER_VALIDATE_URL) ? $siteLogo : url('/' . ltrim($siteLogo, '/'));
                    
                    $subject = "Thank you for your FMZ CME registration ({$regId})";
                    $content = "Hello {$cme->first_name},\n\nThank you for registering to become an FMZ Certified Micro Entrepreneur (CME)!\n\nYour Registration Details:\n- Registration ID: {$regId}\n- Selected Programmes: {$programmesList}\n- Location: {$cme->city}, {$cme->state} ({$cme->pincode})\n- Contact Mobile: {$cme->mobile}\n\nWhat happens next:\n1. Shortlisting: Our recruitment team will review your application against our initial intake quota.\n2. Information Meeting: If shortlisted, you will be invited via WhatsApp to an information meeting.\n3. Training & Certification: Comprehensive training with genuine Nu Skin devices and health equipment before your first client.\n\nIf you have any questions, feel free to reply to this email or message us on WhatsApp at {$whatsappNum}.\n\nWarm regards,\nFMZ India Talent & Recruitment Team";
                    
                    Mail::to($cme->email)->send(new TemplateMail($subject, $content, $logoUrl));
                    Log::info("Direct CME thank-you email sent to {$cme->email}");
                } catch (\Throwable $err) {
                    Log::error("Failed to send direct CME thank-you email: " . $err->getMessage());
                }
            }
        }

        // 2. Notify business admin
        $adminEmail = Setting::get('business_email', 'fmz@fmzau.com.au');
        if (!empty($adminEmail)) {
            $sent = self::send('admin_new_cme', $adminEmail, $vars);
            if (!$sent) {
                try {
                    $siteLogo = Setting::get('site_logo', 'images/FMZ Glow logo.jpg');
                    $logoUrl = filter_var($siteLogo, FILTER_VALIDATE_URL) ? $siteLogo : url('/' . ltrim($siteLogo, '/'));
                    $subject = "🔔 New CME Registration ({$regId}) - {$fullName}";
                    $content = "A new candidate has registered for the FMZ Certified Micro Entrepreneur (CME) programme.\n\nApplicant Details:\n- Registration ID: {$regId}\n- Name: {$fullName}\n- Mobile/WhatsApp: {$cme->mobile}\n- Email: " . ($cme->email ?: 'Not provided') . "\n- Location: {$cme->city}, {$cme->state} ({$cme->pincode})\n- Programmes: {$programmesList}\n- Highest Qualification: {$cme->highest_qualification}\n- Vehicle & Licence: {$cme->vehicle} ({$cme->driving_licence})\n- Referral Source: {$cme->referral_source}\n\nPlease visit the Admin Portal to review their application and update recruitment status.";
                    
                    Mail::to($adminEmail)->send(new TemplateMail($subject, $content, $logoUrl));
                    Log::info("Admin CME alert sent to {$adminEmail}");
                } catch (\Throwable $err) {
                    Log::error("Failed to send admin CME alert: " . $err->getMessage());
                }
            }
        }
    }
}
