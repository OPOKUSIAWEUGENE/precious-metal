<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Gateways\CreateCheckoutRequest;
use App\Http\Requests\v0\Gateways\RefundPaymentRequest;
use App\Modules\v0\Gateways\Services\OnlineCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    protected OnlineCheckoutService $checkoutService;

    public function __construct(OnlineCheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Create a new checkout session.
     */
    public function createCheckout(CreateCheckoutRequest $request): JsonResponse
    {
        return $this->checkoutService->createCheckout($request->validated());
    }

    /**
     * Verify a payment.
     */
    public function verifyPayment(string $paymentId): JsonResponse
    {
        return $this->checkoutService->verifyPayment($paymentId);
    }

    /**
     * Refund a payment.
     */
    public function refundPayment(RefundPaymentRequest $request, string $paymentId): JsonResponse
    {
        return $this->checkoutService->refundPayment($paymentId, $request->input('amount'));
    }

    /**
     * Handle webhook callback from payment gateway.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        return $this->checkoutService->handleWebhook($request->all());
    }

    /**
     * Get gateway status/info.
     */
    public function getGatewayInfo(): JsonResponse
    {
        return response()->json([
            'gateway' => $this->checkoutService->getGatewayName(),
            'status' => 'active',
        ]);
    }
}