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
     * Create Razorpay Order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        
        $key = Setting::get('razorpay_key', config('services.razorpay.key', 'rzp_test_placeholder'));
        $secret = Setting::get('razorpay_secret', config('services.razorpay.secret', 'rzp_secret_placeholder'));

        if (!$key || $key === 'rzp_test_placeholder') {
            return response()->json([
                'success' => false,
                'message' => 'Online payment gateway is in test mode or not configured. You can pay after your visit via UPI or Cash.',
                'mock_allowed' => true,
            ]);
        }

        try {
            $api = new Api($key, $secret);

            $amountInPaise = (int) round($booking->total_amount * 100);
            if ($amountInPaise <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid booking amount for payment.',
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

        // If mock / test mode bypass
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
}
