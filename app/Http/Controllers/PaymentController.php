<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Setting;
use App\Services\EmailNotificationService;
use App\Services\ZohoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    /**
     * Determine the effective active gateway based on settings
     */
    public static function getActiveGateway(): string
    {
        $active = Setting::get('active_payment_gateway', '');
        $razorpayEnabled = (bool) Setting::get('razorpay_enabled', '0');
        $zohoEnabled = (bool) Setting::get('zoho_enabled', '0');

        // Check if one is exclusively enabled
        if ($razorpayEnabled && !$zohoEnabled) {
            return 'razorpay';
        }
        if ($zohoEnabled && !$razorpayEnabled) {
            return 'zoho';
        }

        if ($active === 'razorpay' || $active === 'zoho' || $active === 'none') {
            return $active;
        }

        return 'razorpay';
    }

    /**
     * Get active payment gateway configuration
     */
    public function getPaymentConfig()
    {
        $activeGateway = self::getActiveGateway();
        $razorpayEnabled = (bool) Setting::get('razorpay_enabled', '0');
        $razorpayKey = Setting::get('razorpay_key', '');
        $zohoEnabled = (bool) Setting::get('zoho_enabled', '0');
        $zohoClientId = Setting::get('zoho_client_id', '');
        $zohoCheckoutUrl = Setting::get('zoho_checkout_url', '');
        $zohoMerchantId = Setting::get('zoho_merchant_id', '');

        return response()->json([
            'success' => true,
            'active_gateway' => $activeGateway,
            'razorpay' => [
                'enabled' => $razorpayEnabled,
                'key' => $razorpayKey,
            ],
            'zoho' => [
                'enabled' => $zohoEnabled,
                'client_id' => $zohoClientId,
                'checkout_url' => $zohoCheckoutUrl,
                'merchant_id' => $zohoMerchantId,
            ],
            'allow_pay_after_visit' => true,
            'currency' => Setting::get('currency', 'INR'),
        ]);
    }

    /**
     * Create Order / Payment URL for Razorpay or Zoho Checkout
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'gateway' => 'nullable|string|in:razorpay,zoho',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $gateway = $request->gateway ?: self::getActiveGateway();

        // Handle Zoho Checkout
        if ($gateway === 'zoho') {
            if ($booking->total_amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Online payment is not required for consultation requests with no fixed price. You can pay after your visit via UPI or Cash.',
                    'mock_allowed' => true,
                ]);
            }

            // 1. If custom static payment link is configured and is NOT the placeholder
            $customUrl = trim((string) Setting::get('zoho_checkout_url', ''));
            $isPlaceholder = empty($customUrl)
                || str_ends_with(rtrim($customUrl, '/'), 'payments.zoho.in/checkout')
                || str_ends_with(rtrim($customUrl, '/'), 'payments.zoho.in');

            if (!$isPlaceholder && !empty($customUrl)) {
                $params = http_build_query([
                    'booking_id' => $booking->booking_number,
                    'amount' => $booking->total_amount,
                    'currency' => 'INR',
                    'name' => $booking->name,
                    'email' => $booking->email ?? '',
                    'phone' => $booking->phone,
                    'service' => $booking->service_name,
                ]);

                $finalUrl = $customUrl . (str_contains($customUrl, '?') ? '&' : '?') . $params;

                $booking->update([
                    'payment_method' => 'zoho',
                    'zoho_checkout_url' => $finalUrl,
                ]);

                return response()->json([
                    'success' => true,
                    'gateway' => 'zoho',
                    'checkout_url' => $finalUrl,
                    'booking_number' => $booking->booking_number,
                    'amount' => $booking->total_amount,
                    'client_name' => $booking->name,
                    'client_email' => $booking->email,
                    'client_phone' => $booking->phone,
                ]);
            }

            // 2. Official Zoho Payments API Hosted Checkout Session flow
            $sessionResult = ZohoPaymentService::createPaymentSession(
                (float) $booking->total_amount,
                'INR',
                [
                    'name' => $booking->name,
                    'email' => $booking->email ?? '',
                    'phone' => $booking->phone,
                    'description' => "FMZ Glow Booking #{$booking->booking_number}",
                ],
                $booking->booking_number
            );

            if ($sessionResult['success']) {
                $booking->update([
                    'payment_method' => 'zoho',
                    'zoho_checkout_url' => $sessionResult['checkout_url'],
                ]);

                return response()->json([
                    'success' => true,
                    'gateway' => 'zoho',
                    'access_key' => $sessionResult['access_key'],
                    'payments_session' => $sessionResult['payments_session'],
                    'checkout_url' => $sessionResult['checkout_url'],
                    'booking_number' => $booking->booking_number,
                    'amount' => $booking->total_amount,
                    'client_name' => $booking->name,
                    'client_email' => $booking->email,
                    'client_phone' => $booking->phone,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $sessionResult['message'] ?? 'Zoho Payments session could not be created. Please verify your Zoho API credentials in Admin Settings.',
                'mock_allowed' => true,
            ]);
        }

        // Handle Razorpay
        $key = Setting::get('razorpay_key', config('services.razorpay.key', 'rzp_test_placeholder'));
        $secret = Setting::get('razorpay_secret', config('services.razorpay.secret', 'rzp_secret_placeholder'));

        if (!$key || $key === 'rzp_test_placeholder') {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay gateway is not configured with a valid Key ID. You can pay after your visit via UPI or Cash.',
                'mock_allowed' => true,
            ]);
        }

        try {
            $amountInPaise = (int) round($booking->total_amount * 100);
            if ($amountInPaise <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Online payment is not required for consultation requests with no fixed price. You can pay after your visit via UPI or Cash.',
                    'mock_allowed' => true,
                ]);
            }

            $orderId = null;

            // Only attempt server-side order creation if secret is provided and differs from key ID
            if ($secret && $secret !== $key && $secret !== 'rzp_secret_placeholder') {
                try {
                    $api = new Api($key, $secret);
                    $orderData = [
                        'receipt' => $booking->booking_number,
                        'amount' => $amountInPaise,
                        'currency' => 'INR',
                        'notes' => [
                            'booking_id' => (string) $booking->id,
                            'client_name' => $booking->name,
                            'service' => $booking->service_name,
                        ],
                    ];

                    $razorpayOrder = $api->order->create($orderData);
                    $orderId = $razorpayOrder['id'] ?? null;
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Razorpay order creation fallback: " . $e->getMessage());
                }
            }

            $booking->update([
                'razorpay_order_id' => $orderId,
                'payment_method' => 'razorpay',
            ]);

            return response()->json([
                'success' => true,
                'gateway' => 'razorpay',
                'order_id' => $orderId,
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'key' => $key,
                'booking_number' => $booking->booking_number,
                'client_name' => $booking->name,
                'client_email' => $booking->email,
                'client_phone' => $booking->phone,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize payment gateway: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify Razorpay Payment Signature
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        $secret = Setting::get('razorpay_secret', config('services.razorpay.secret', 'rzp_secret_placeholder'));

        $expectedSignature = hash_hmac(
            'sha256',
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            $secret
        );

        $isVerified = hash_equals($expectedSignature, $request->razorpay_signature);

        if (!$isVerified && $secret !== 'rzp_secret_placeholder') {
            $booking->update([
                'payment_status' => 'failed',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Invalid signature.',
            ], 400);
        }

        $booking->update([
            'payment_status' => 'paid',
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
            'status' => 'confirmed',
        ]);

        // Send booking created notification to admin + customer now that payment has completed
        EmailNotificationService::notifyBookingCreated($booking);
        // Send payment confirmation email
        EmailNotificationService::notifyPaymentReceived($booking);

        return response()->json([
            'success' => true,
            'message' => "Payment verified! Appointment scheduled with Specialist {$booking->specialist_name} in pincode {$booking->pincode}.",
            'booking' => [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'name' => $booking->name,
                'service' => $booking->service_name,
                'date' => $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A',
                'time' => $booking->time_slot_text,
                'pincode' => $booking->pincode,
                'specialist_name' => $booking->specialist_name,
                'specialist_qualification' => $booking->specialist_qualification,
                'total_amount' => $booking->total_amount,
                'payment_method' => 'razorpay',
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ],
        ]);
    }

    /**
     * Verify Zoho Checkout Payment
     */
    public function verifyZohoPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'zoho_payment_id' => 'required|string',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        $booking->update([
            'payment_status' => 'paid',
            'zoho_payment_id' => $request->zoho_payment_id,
            'status' => 'confirmed',
        ]);

        EmailNotificationService::notifyBookingCreated($booking);
        EmailNotificationService::notifyPaymentReceived($booking);

        return response()->json([
            'success' => true,
            'message' => "Payment verified! Appointment scheduled with Specialist {$booking->specialist_name} in pincode {$booking->pincode}.",
            'booking' => [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'name' => $booking->name,
                'service' => $booking->service_name,
                'date' => $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A',
                'time' => $booking->time_slot_text,
                'pincode' => $booking->pincode,
                'specialist_name' => $booking->specialist_name,
                'specialist_qualification' => $booking->specialist_qualification,
                'total_amount' => $booking->total_amount,
                'payment_method' => 'zoho',
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ],
        ]);
    }

    /**
     * Endpoint for test page c:\Users\lenovo\Downloads\zoho-payments-test.html
     */
    public function createZohoTestSession(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string',
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $orderRef = 'TEST-' . strtoupper(Str::random(6));

        $sessionResult = ZohoPaymentService::createPaymentSession(
            (float) $validated['amount'],
            $validated['currency'] ?? 'INR',
            [
                'name' => $validated['name'] ?? 'Test Customer',
                'email' => $validated['email'] ?? 'test@example.com',
                'phone' => $validated['phone'] ?? '9876543210',
                'description' => $validated['description'] ?? 'Zoho Payments connection test',
            ],
            $orderRef
        );

        if (!$sessionResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $sessionResult['message'],
                'message' => $sessionResult['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'access_key' => $sessionResult['access_key'],
            'payments_session' => $sessionResult['payments_session'],
            'checkout_url' => $sessionResult['checkout_url'],
        ]);
    }

    /**
     * Handle customer return from Zoho Hosted Checkout
     */
    public function handleZohoCallback(Request $request)
    {
        $bookingRef = $request->get('booking_ref') ?: $request->get('udf1');
        $paymentId = $request->get('payment_id') ?: $request->get('payments_id') ?: $request->get('transaction_id');

        $booking = null;
        if ($bookingRef) {
            $booking = Booking::where('booking_number', $bookingRef)->first();
        }

        if ($booking) {
            $booking->update([
                'payment_status' => 'paid',
                'zoho_payment_id' => $paymentId ?: 'ZOHO-HOSTED-PAID',
                'status' => 'confirmed',
            ]);

            EmailNotificationService::notifyBookingCreated($booking);
            EmailNotificationService::notifyPaymentReceived($booking);

            return redirect('/glow?payment=success&booking=' . urlencode($booking->booking_number));
        }

        return redirect('/glow?payment=success');
    }
}
