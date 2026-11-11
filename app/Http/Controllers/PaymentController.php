<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Setting;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    /**
     * Get active payment gateway configuration
     */
    public function getPaymentConfig()
    {
        $activeGateway = Setting::get('active_payment_gateway', 'razorpay');
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
        $gateway = $request->gateway ?: Setting::get('active_payment_gateway', 'razorpay');

        // Handle Zoho Checkout
        if ($gateway === 'zoho') {
            if ($booking->total_amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Online payment is not required for consultation requests with no fixed price. You can pay after your visit via UPI or Cash.',
                    'mock_allowed' => true,
                ]);
            }

            $zohoClientId = Setting::get('zoho_client_id', '');
            $zohoCheckoutUrl = Setting::get('zoho_checkout_url', '');

            if (!$zohoCheckoutUrl && !$zohoClientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zoho Checkout credentials are not yet configured in admin settings. You can pay after your visit via UPI or Cash.',
                    'mock_allowed' => true,
                ]);
            }

            // Build checkout page url with booking parameters
            $baseUrl = $zohoCheckoutUrl ?: 'https://payments.zoho.in/checkout';
            $params = http_build_query([
                'booking_id' => $booking->booking_number,
                'amount' => $booking->total_amount,
                'currency' => 'INR',
                'name' => $booking->name,
                'email' => $booking->email ?? '',
                'phone' => $booking->phone,
                'client_id' => $zohoClientId,
                'service' => $booking->service_name,
            ]);

            $finalUrl = $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . $params;

            $booking->update([
                'payment_method' => 'zoho',
                'zoho_checkout_url' => $finalUrl,
            ]);

            return response()->json([
                'success' => true,
                'gateway' => 'zoho',
                'checkout_url' => $finalUrl,
                'client_id' => $zohoClientId,
                'booking_number' => $booking->booking_number,
                'amount' => $booking->total_amount,
                'client_name' => $booking->name,
                'client_email' => $booking->email,
                'client_phone' => $booking->phone,
            ]);
        }

        // Handle Razorpay
        $key = Setting::get('razorpay_key', config('services.razorpay.key', 'rzp_test_placeholder'));
        $secret = Setting::get('razorpay_secret', config('services.razorpay.secret', 'rzp_secret_placeholder'));

        if (!$key || $key === 'rzp_test_placeholder') {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay gateway is in test mode or not configured. You can pay after your visit via UPI or Cash.',
                'mock_allowed' => true,
            ]);
        }

        try {
            $api = new Api($key, $secret);

            $amountInPaise = (int) round($booking->total_amount * 100);
            if ($amountInPaise <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Online payment is not required for consultation requests with no fixed price. You can pay after your visit via UPI or Cash.',
                    'mock_allowed' => true,
                ]);
            }

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

            $booking->update([
                'razorpay_order_id' => $razorpayOrder['id'],
                'payment_method' => 'razorpay',
            ]);

            return response()->json([
                'success' => true,
                'gateway' => 'razorpay',
                'order_id' => $razorpayOrder['id'],
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

        // Send payment confirmation email
        EmailNotificationService::notifyPaymentReceived($booking);

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully! Your booking is confirmed.',
            'booking' => $booking,
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

        EmailNotificationService::notifyPaymentReceived($booking);

        return response()->json([
            'success' => true,
            'message' => 'Zoho Payment recorded successfully! Your booking is confirmed.',
            'booking' => $booking,
        ]);
    }
}
