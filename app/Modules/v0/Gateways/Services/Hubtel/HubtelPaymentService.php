<?php

namespace App\Modules\v0\Gateways\Services\Hubtel;

use App\Modules\v0\Gateways\Interfaces\PaymentGatewayInterface;
use App\Modules\v0\Finances\Models\Payment;
use App\Utils\AppResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HubtelPaymentService implements PaymentGatewayInterface
{
    protected ?string $baseUrl = null;
    protected ?string $clientId = null;
    protected ?string $clientSecret = null;
    protected ?string $merchantAccountNumber = null;

    public function __construct()
    {
        $this->baseUrl = config('services.hubtel.base_url', 'https://api.hubtel.com');
        $this->clientId = config('services.hubtel.client_id', env('HUBTEL_CLIENT_ID'));
        $this->clientSecret = config('services.hubtel.client_secret', env('HUBTEL_CLIENT_SECRET'));
        $this->merchantAccountNumber = config('services.hubtel.merchant_account', env('HUBTEL_MERCHANT_ACCOUNT'));
    }

    /**
     * Initialize a payment transaction via Hubtel Online Checkout.
     */
    public function initializePayment(array $data): JsonResponse
    {
        try {
            $amount = $data['amount'] ?? 0;
            $customerName = $data['customer_name'] ?? 'Customer';
            $customerEmail = $data['customer_email'] ?? '';
            $customerPhone = $data['customer_phone'] ?? '';
            $paymentId = $data['payment_id'] ?? null;
            $description = $data['description'] ?? 'Payment for booking';

            $callbackUrl = config('services.hubtel.callback_url', env('HUBTEL_CALLBACK_URL'));
            $returnUrl = config('services.hubtel.return_url', env('HUBTEL_RETURN_URL'));

            $payload = [
                'totalAmount' => $amount,
                'currency' => 'GHS',
                'merchantId' => $this->merchantAccountNumber,
                'customerEmail' => $customerEmail,
                'customerPhoneNumber' => $customerPhone,
                'customerName' => $customerName,
                'paymentChannel' => ['MobileMoney', 'Card'],
                'description' => $description,
                'returnUrl' => $returnUrl,
                'callbackUrl' => $callbackUrl,
                'customFields' => [
                    [
                        'name' => 'payment_id',
                        'value' => $paymentId
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/onlinecheckout/mpayment/create', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                
                return AppResponse::success([
                    'checkout_token' => $responseData['token'] ?? null,
                    'checkout_url' => $responseData['checkoutUrl'] ?? null,
                    'transaction_id' => $responseData['transactionId'] ?? null,
                    'status' => $responseData['status'] ?? 'pending',
                    'message' => $responseData['message'] ?? 'Payment initialized successfully',
                ], 'Payment initialized successfully');
            }

            Log::error('Hubtel payment initialization failed', [
                'response' => $response->json(),
                'payment_id' => $paymentId
            ]);

            return AppResponse::error('Failed to initialize payment: ' . ($response->json()['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Hubtel payment initialization exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return AppResponse::error('Payment initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify a payment transaction.
     */
    public function verifyPayment(string $transactionId): JsonResponse
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/v1/onlinecheckout/mpayment/status/' . $this->merchantAccountNumber . '/' . $transactionId);

            if ($response->successful()) {
                $responseData = $response->json();
                
                return AppResponse::success([
                    'transaction_id' => $transactionId,
                    'status' => $responseData['status'] ?? 'unknown',
                    'amount' => $responseData['amount'] ?? 0,
                    'currency' => $responseData['currency'] ?? 'GHS',
                    'description' => $responseData['description'] ?? '',
                    'customer_name' => $responseData['customerName'] ?? '',
                    'customer_email' => $responseData['customerEmail'] ?? '',
                    'customer_phone' => $responseData['customerPhoneNumber'] ?? '',
                    'completed_at' => $responseData['completedAt'] ?? null,
                ], 'Payment verified successfully');
            }

            return AppResponse::error('Failed to verify payment');
        } catch (\Exception $e) {
            Log::error('Hubtel payment verification exception', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);
            return AppResponse::error('Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Process a refund.
     */
    public function refund(string $transactionId, float $amount): JsonResponse
    {
        try {
            $payload = [
                'amount' => $amount,
                'currency' => 'GHS',
                'description' => 'Refund for transaction ' . $transactionId,
                'transactionId' => $transactionId,
                'merchantId' => $this->merchantAccountNumber
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/onlinecheckout/mpayment/refund', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                
                return AppResponse::success([
                    'refund_id' => $responseData['refundId'] ?? null,
                    'status' => $responseData['status'] ?? 'success',
                    'amount' => $amount,
                ], 'Refund processed successfully');
            }

            return AppResponse::error('Failed to process refund');
        } catch (\Exception $e) {
            Log::error('Hubtel refund exception', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
                'amount' => $amount
            ]);
            return AppResponse::error('Refund failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle webhook/callback from Hubtel.
     */
    public function handleCallback(array $data): JsonResponse
    {
        try {
            Log::info('Hubtel callback received', $data);

            $status = $data['status'] ?? '';
            $transactionId = $data['transactionId'] ?? '';
            $amount = $data['amount'] ?? 0;

            // Find the payment by transaction ID
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                Log::warning('Payment not found for transaction', ['transaction_id' => $transactionId]);
                return AppResponse::error('Payment not found', 404);
            }

            // Update payment status based on Hubtel response
            switch (strtoupper($status)) {
                case 'SUCCESS':
                case 'COMPLETED':
                    $payment->status = 'COMPLETED';
                    $payment->paid_at = now();
                    break;
                case 'FAILED':
                    $payment->status = 'FAILED';
                    $payment->failed_at = now();
                    break;
                case 'PENDING':
                case 'PROCESSING':
                    $payment->status = 'PROCESSING';
                    break;
                default:
                    $payment->status = 'PENDING';
            }

            // Store the gateway response
            $payment->gateway_response = $data;
            $payment->save();

            return AppResponse::success($payment, 'Callback processed successfully');
        } catch (\Exception $e) {
            Log::error('Hubtel callback exception', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return AppResponse::error('Callback processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the gateway name.
     */
    public function getGatewayName(): string
    {
        return 'HUBTEL';
    }

    /**
     * Get the OAuth token for Hubtel API.
     */
    protected function getAccessToken(): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post($this->baseUrl . '/token', [
            'grant_type' => 'client_credentials'
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get Hubtel access token');
    }
}