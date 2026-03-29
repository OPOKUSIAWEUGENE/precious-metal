<?php

namespace App\Http\Controllers\v0\Gateways;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Gateways\CreateCheckoutRequest;
use App\Http\Requests\v0\Gateways\RefundPaymentRequest;
use App\Modules\v0\Gateways\Services\OnlineCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected OnlineCheckoutService $checkoutService;

    public function __construct(OnlineCheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Create a new checkout session.
     */
    public function create(CreateCheckoutRequest $request): JsonResponse
    {
        return $this->checkoutService->createCheckout($request->validated());
    }

    /**
     * Verify a payment.
     */
    public function verify(string $paymentId): JsonResponse
    {
        return $this->checkoutService->verifyPayment($paymentId);
    }

    /**
     * Refund a payment.
     */
    public function refund(RefundPaymentRequest $request, string $paymentId): JsonResponse
    {
        return $this->checkoutService->refundPayment($paymentId, $request->input('amount'));
    }

    /**
     * Handle webhook callback from payment gateway.
     */
    public function webhook(Request $request): JsonResponse
    {
        return $this->checkoutService->handleWebhook($request->all());
    }

    /**
     * Get gateway status/info.
     */
    public function info(): JsonResponse
    {
        return response()->json([
            'gateway' => $this->checkoutService->getGatewayName(),
            'status' => 'active',
        ]);
    }
}