<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoPaymentService
{
    /**
     * Get Zoho OAuth Access Token using Refresh Token or Direct Token
     */
    public static function getAccessToken(): ?string
    {
        // 1. Check if a direct manual access token is provided in settings
        $directToken = trim((string) Setting::get('zoho_access_token', ''));
        if (!empty($directToken)) {
            return $directToken;
        }

        // 2. Check cache for refreshed access token
        $cachedToken = Cache::get('zoho_payments_oauth_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        // 3. Generate new access token using Refresh Token flow
        $clientId = trim((string) Setting::get('zoho_client_id', ''));
        $clientSecret = trim((string) Setting::get('zoho_client_secret', ''));
        $refreshToken = trim((string) Setting::get('zoho_refresh_token', ''));

        if (empty($clientId) || empty($clientSecret) || empty($refreshToken)) {
            Log::warning('Zoho Payments: Client ID, Client Secret, or Refresh Token missing.');
            return null;
        }

        try {
            $response = Http::asForm()->timeout(15)->post('https://accounts.zoho.in/oauth/v2/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
            ]);

            $data = $response->json();
            if ($response->successful() && !empty($data['access_token'])) {
                $expiresIn = max(60, ($data['expires_in'] ?? 3600) - 300);
                Cache::put('zoho_payments_oauth_token', $data['access_token'], $expiresIn);
                return $data['access_token'];
            }

            Log::error('Zoho OAuth Token generation failed: ' . json_encode($data));
            return null;
        } catch (\Throwable $e) {
            Log::error('Zoho OAuth Token request exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a Zoho Payments Session for Hosted Checkout
     * POST https://payments.zoho.in/api/v1/paymentsessions?account_id={account_id}
     */
    public static function createPaymentSession(float $amount, string $currency, array $customer, string $orderRef): array
    {
        $accountId = trim((string) (Setting::get('zoho_merchant_id') ?: Setting::get('zoho_account_id', '')));
        if (empty($accountId)) {
            return [
                'success' => false,
                'message' => 'Zoho Payments Account ID is not configured in Admin Settings > Payment Gateways.',
            ];
        }

        $accessToken = self::getAccessToken();
        if (empty($accessToken)) {
            return [
                'success' => false,
                'message' => 'Unable to obtain Zoho OAuth access token. Please verify your Client ID, Client Secret, and Refresh Token in Admin Settings.',
            ];
        }

        $formattedAmount = number_format($amount, 2, '.', '');
        $successUrl = url('/api/payment/zoho/callback') . '?booking_ref=' . urlencode($orderRef);
        $failureUrl = url('/glow') . '?payment_status=cancelled&ref=' . urlencode($orderRef);

        $payload = [
            'amount' => $formattedAmount,
            'currency' => $currency ?: 'INR',
            'configurations' => [
                'allowed_payment_methods' => ['card', 'upi', 'netbanking'],
                'hosted_checkout_parameters' => [
                    'phone' => $customer['phone'] ?? '',
                    'email' => $customer['email'] ?? '',
                    'description' => $customer['description'] ?? "Booking #{$orderRef}",
                    'success_url' => $successUrl,
                    'failure_url' => $failureUrl,
                    'udf1' => $orderRef,
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(20)->post("https://payments.zoho.in/api/v1/paymentsessions?account_id={$accountId}", $payload);

            $data = $response->json();

            $accessKey = $data['payments_session']['access_key'] ?? $data['access_key'] ?? null;
            if ($response->successful() && $accessKey) {
                $checkoutUrl = "https://payments.zoho.in/hostedcheckout/" . urlencode($accessKey);
                return [
                    'success' => true,
                    'access_key' => $accessKey,
                    'payments_session' => $data['payments_session'] ?? ['access_key' => $accessKey],
                    'checkout_url' => $checkoutUrl,
                ];
            }

            $errMsg = $data['message'] ?? ($data['error_description'] ?? 'Zoho Payments API returned an error.');
            Log::error("Zoho paymentsession creation failed ({$response->status()}): " . json_encode($data));
            return [
                'success' => false,
                'message' => $errMsg,
                'details' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('Zoho paymentsession exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Network error connecting to Zoho Payments API: ' . $e->getMessage(),
            ];
        }
    }
}

