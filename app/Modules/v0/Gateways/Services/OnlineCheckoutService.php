<?php

namespace App\Modules\v0\Gateways\Services;

use App\Modules\v0\Gateways\Interfaces\PaymentGatewayInterface;
use App\Modules\v0\Gateways\Services\Hubtel\HubtelPaymentService;
use App\Modules\v0\Finances\Models\Payment;
use App\Modules\v0\Finances\Enums\PaymentStatus;
use App\Modules\v0\Finances\Enums\PaymentMethod;
use App\Utils\AppResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OnlineCheckoutService
{
    protected PaymentGatewayInterface $gateway;
    protected string $defaultGateway;

    public function __construct()
    {
        $this->defaultGateway = config('services.default_gateway', 'HUBTEL');
        $this->gateway = $this->resolveGateway();
    }

    /**
     * Resolve the payment gateway based on configuration.
     */
    protected function resolveGateway(): PaymentGatewayInterface
    {
        return match ($this->defaultGateway) {
            'HUBTEL' => new HubtelPaymentService(),
            default => new HubtelPaymentService(),
        };
    }

    /**
     * Create a checkout session for a payment.
     */
    public function createCheckout(array $data): JsonResponse
    {
        try {
            // Validate required fields
            $bookingId = $data['booking_id'] ?? null;
            $passengerProfileId = $data['passenger_profile_id'] ?? null;
            $operatorId = $data['operator_id'] ?? null;
            $amount = $data['amount'] ?? 0;
            $customerName = $data['customer_name'] ?? 'Customer';
            $customerEmail = $data['customer_email'] ?? '';
            $customerPhone = $data['customer_phone'] ?? '';
            $description = $data['description'] ?? 'Payment for booking';

            if (!$bookingId || !$passengerProfileId || $amount <= 0) {
                return AppResponse::error('Invalid payment data. Please provide booking_id, passenger_profile_id, and amount.');
            }

            // Create a pending payment record
            $payment = Payment::create([
                'booking_id' => $bookingId,
                'passenger_profile_id' => $passengerProfileId,
                'operator_id' => $operatorId,
                'amount' => $amount,
                'currency' => 'GHS',
                'payment_method' => PaymentMethod::MOBILE_MONEY,
                'status' => PaymentStatus::PENDING,
                'gateway' => $this->gateway->getGatewayName(),
            ]);

            // Initialize payment with the gateway
            $gatewayResponse = $this->gateway->initializePayment([
                'amount' => $amount,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'payment_id' => $payment->id,
                'description' => $description,
            ]);

            // If gateway initialization failed, update payment status
            if (!$gatewayResponse->getData(true)['success'] ?? false) {
                $payment->status = PaymentStatus::FAILED;
                $payment->notes = 'Gateway initialization failed';
                $payment->save();
                
                return AppResponse::error('Payment initialization failed: ' . ($gatewayResponse->getData(true)['message'] ?? 'Unknown error'));
            }

            $gatewayData = $gatewayResponse->getData(true)['data'] ?? [];

            // Update payment with transaction ID
            $payment->transaction_id = $gatewayData['transaction_id'] ?? null;
            $payment->external_reference = $gatewayData['checkout_token'] ?? null;
            $payment->gateway_response = $gatewayData;
            $payment->save();

            return AppResponse::success([
                'payment_id' => $payment->id,
                'checkout_token' => $gatewayData['checkout_token'] ?? null,
                'checkout_url' => $gatewayData['checkout_url'] ?? null,
                'transaction_id' => $payment->transaction_id,
                'amount' => $amount,
                'currency' => 'GHS',
            ], 'Checkout created successfully');
        } catch (\Exception $e) {
            return AppResponse::error('Checkout creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify a payment completion.
     */
    public function verifyPayment(string $paymentId): JsonResponse
    {
        try {
            $payment = Payment::find($paymentId);

            if (!$payment) {
                return AppResponse::notFound('Payment not found');
            }

            if (!$payment->transaction_id) {
                return AppResponse::error('No transaction ID found for this payment');
            }

            // Verify with the gateway
            $verificationResponse = $this->gateway->verifyPayment($payment->transaction_id);

            if (!$verificationResponse->getData(true)['success'] ?? false) {
                return AppResponse::error('Payment verification failed');
            }

            $verificationData = $verificationResponse->getData(true)['data'] ?? [];
            $status = strtoupper($verificationData['status'] ?? '');

            // Update payment status based on verification
            if (in_array($status, ['SUCCESS', 'COMPLETED'])) {
                $payment->status = PaymentStatus::COMPLETED;
                $payment->paid_at = now();
            } elseif ($status === 'FAILED') {
                $payment->status = PaymentStatus::FAILED;
                $payment->failed_at = now();
            } elseif (in_array($status, ['PENDING', 'PROCESSING'])) {
                $payment->status = PaymentStatus::PROCESSING;
            }

            $payment->gateway_response = $verificationData;
            $payment->save();

            return AppResponse::success([
                'payment_id' => $payment->id,
                'status' => $payment->status->value ?? $payment->status,
                'amount' => $payment->amount,
                'verified_at' => now()->toISOString(),
            ], 'Payment verified successfully');
        } catch (\Exception $e) {
            return AppResponse::error('Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Process a refund for a payment.
     */
    public function refundPayment(string $paymentId, ?float $amount = null): JsonResponse
    {
        try {
            $payment = Payment::find($paymentId);

            if (!$payment) {
                return AppResponse::notFound('Payment not found');
            }

            if ($payment->status !== PaymentStatus::COMPLETED) {
                return AppResponse::error('Only completed payments can be refunded');
            }

            $refundAmount = $amount ?? $payment->amount;

            // Process refund with gateway
            $refundResponse = $this->gateway->refund($payment->transaction_id, $refundAmount);

            if (!$refundResponse->getData(true)['success'] ?? false) {
                return AppResponse::error('Refund processing failed');
            }

            // Update payment status to refunded
            $payment->status = PaymentStatus::REFUNDED;
            $payment->refunded_at = now();
            $payment->notes = 'Refunded: ' . $refundAmount;
            $payment->save();

            return AppResponse::success([
                'payment_id' => $payment->id,
                'refund_amount' => $refundAmount,
                'refunded_at' => $payment->refunded_at->toISOString(),
            ], 'Payment refunded successfully');
        } catch (\Exception $e) {
            return AppResponse::error('Refund failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle webhook/callback from the payment gateway.
     */
    public function handleWebhook(array $payload): JsonResponse
    {
        return $this->gateway->handleCallback($payload);
    }

    /**
     * Get the current gateway name.
     */
    public function getGatewayName(): string
    {
        return $this->gateway->getGatewayName();
    }
}