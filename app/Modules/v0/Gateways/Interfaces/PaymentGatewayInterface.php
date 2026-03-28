<?php

namespace App\Modules\v0\Gateways\Interfaces;

use App\Modules\v0\Finances\Models\Payment;
use Illuminate\Http\JsonResponse;

interface PaymentGatewayInterface
{
    /**
     * Initialize a payment transaction.
     */
    public function initializePayment(array $data): JsonResponse;

    /**
     * Verify a payment transaction.
     */
    public function verifyPayment(string $transactionId): JsonResponse;

    /**
     * Process a refund.
     */
    public function refund(string $transactionId, float $amount): JsonResponse;

    /**
     * Handle webhook/callback from the payment gateway.
     */
    public function handleCallback(array $data): JsonResponse;

    /**
     * Get the gateway name.
     */
    public function getGatewayName(): string;
}