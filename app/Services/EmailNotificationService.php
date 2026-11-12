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

        // 1. Notify user if email provided
        if (!empty($booking->email)) {
            self::send('booking_received', $booking->email, $vars);
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
}
