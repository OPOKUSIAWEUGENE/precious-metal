<?php

namespace App\Modules\v0\Callbacks\Services;

use App\Modules\v0\Callbacks\Interfaces\CallbackInterface;
use App\Modules\v0\Callbacks\Models\Callback;
use App\Modules\v0\Callbacks\Enums\CallbackStatus;
use App\Modules\v0\Callbacks\Enums\CallbackType;
use App\Utils\AppResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallbackService implements CallbackInterface
{
    protected int $maxRetries = 3;

    /**
     * Process an incoming callback (webhook).
     */
    public function processIncoming(array $data): JsonResponse
    {
        try {
            // Determine callback type based on data
            $callbackType = $this->determineCallbackType($data);

            // Create incoming callback record
            $callback = Callback::create([
                'type' => CallbackType::INCOMING,
                'status' => CallbackStatus::SENT,
                'endpoint' => request()->fullUrl(),
                'method' => request()->method(),
                'payload' => $data,
                'headers' => request()->headers->all(),
                'sent_at' => now(),
                'delivered_at' => now(),
            ]);

            // Process the callback based on its nature (e.g., payment, booking)
            $response = $this->processCallbackData($callbackType, $data);

            if ($response['success']) {
                $callback->status = CallbackStatus::DELIVERED;
                $callback->response = $response['data'];
                $callback->save();
            }

            return AppResponse::success([
                'callback_id' => $callback->id,
                'processed' => true,
            ], 'Callback processed successfully');
        } catch (\Exception $e) {
            Log::error('Incoming callback processing failed', [
                'error' => $e->getMessage(),
                'data' => $data ?? []
            ]);

            return AppResponse::error('Callback processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Create and send an outgoing callback.
     */
    public function sendOutgoing(string $endpoint, array $payload, ?string $relatedType = null, ?string $relatedId = null): JsonResponse
    {
        try {
            // Create callback record
            $callback = Callback::create([
                'type' => CallbackType::OUTGOING,
                'status' => CallbackStatus::PENDING,
                'endpoint' => $endpoint,
                'method' => 'POST',
                'payload' => $payload,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
                'scheduled_at' => now(),
            ]);

            // Send the callback
            $response = $this->sendCallbackRequest($callback);

            $callback->response = $response['data'];
            $callback->status = $response['success'] ? CallbackStatus::SENT : CallbackStatus::FAILED;
            $callback->sent_at = now();

            if ($response['success']) {
                $callback->status = CallbackStatus::DELIVERED;
                $callback->delivered_at = now();
            } else {
                $callback->error_message = $response['error'] ?? 'Unknown error';
            }

            $callback->save();

            return AppResponse::success([
                'callback_id' => $callback->id,
                'status' => $callback->status->value,
                'response' => $response['data'],
            ], $response['success'] ? 'Callback sent successfully' : 'Callback failed');
        } catch (\Exception $e) {
            Log::error('Outgoing callback failed', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);

            return AppResponse::error('Callback sending failed: ' . $e->getMessage());
        }
    }

    /**
     * Get all callbacks.
     */
    public function all(): Collection
    {
        return Callback::orderBy('created_at', 'desc')->get();
    }

    /**
     * Find callback by ID.
     */
    public function find(string $id): ?Callback
    {
        return Callback::find($id);
    }

    /**
     * Get incoming callbacks.
     */
    public function getIncoming(): Collection
    {
        return Callback::where('type', CallbackType::INCOMING)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get outgoing callbacks.
     */
    public function getOutgoing(): Collection
    {
        return Callback::where('type', CallbackType::OUTGOING)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get callbacks by status.
     */
    public function getByStatus(string $status): Collection
    {
        return Callback::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Retry a failed callback.
     */
    public function retry(string $id): JsonResponse
    {
        try {
            $callback = $this->find($id);

            if (!$callback) {
                return AppResponse::notFound('Callback not found');
            }

            if ($callback->retry_count >= $this->maxRetries) {
                return AppResponse::error('Maximum retry attempts reached');
            }

            // Increment retry count
            $callback->retry_count += 1;
            $callback->status = CallbackStatus::PENDING;
            $callback->save();

            // Attempt to resend
            $response = $this->sendCallbackRequest($callback);

            $callback->response = $response['data'];
            $callback->status = $response['success'] ? CallbackStatus::DELIVERED : CallbackStatus::FAILED;

            if ($response['success']) {
                $callback->delivered_at = now();
            } else {
                $callback->error_message = $response['error'] ?? 'Retry failed';
            }

            $callback->save();

            return AppResponse::success([
                'callback_id' => $callback->id,
                'retry_count' => $callback->retry_count,
                'status' => $callback->status->value,
            ], $response['success'] ? 'Callback retry successful' : 'Callback retry failed');
        } catch (\Exception $e) {
            return AppResponse::error('Retry failed: ' . $e->getMessage());
        }
    }

    /**
     * Get callbacks for a related model.
     */
    public function getByRelated(string $relatedType, string $relatedId): Collection
    {
        return Callback::where('related_type', $relatedType)
            ->where('related_id', $relatedId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Determine callback type based on data.
     */
    protected function determineCallbackType(array $data): string
    {
        // Check for payment-related callbacks
        if (isset($data['status']) && isset($data['transactionId'])) {
            return 'payment';
        }

        // Check for booking-related callbacks
        if (isset($data['booking_id']) || isset($data['bookingId'])) {
            return 'booking';
        }

        // Check for trip-related callbacks
        if (isset($data['trip_id']) || isset($data['tripId'])) {
            return 'trip';
        }

        return 'unknown';
    }

    /**
     * Process callback data based on type.
     */
    protected function processCallbackData(string $type, array $data): array
    {
        try {
            switch ($type) {
                case 'payment':
                    // Handle payment callback
                    return $this->handlePaymentCallback($data);
                case 'booking':
                    // Handle booking callback
                    return $this->handleBookingCallback($data);
                case 'trip':
                    // Handle trip callback
                    return $this->handleTripCallback($data);
                default:
                    return ['success' => true, 'data' => ['type' => $type, 'message' => 'Callback received']];
            }
        } catch (\Exception $e) {
            Log::error('Callback data processing failed', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'data' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Handle payment callback.
     */
    protected function handlePaymentCallback(array $data): array
    {
        // Import Payment model
        $paymentModel = \App\Modules\v0\Finances\Models\Payment::class;
        $paymentStatusEnum = \App\Modules\v0\Finances\Enums\PaymentStatus::class;

        $transactionId = $data['transactionId'] ?? null;
        $status = strtoupper($data['status'] ?? '');

        if (!$transactionId) {
            return ['success' => false, 'data' => [], 'error' => 'No transaction ID'];
        }

        $payment = $paymentModel::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return ['success' => false, 'data' => [], 'error' => 'Payment not found'];
        }

        // Update payment status
        if (in_array($status, ['SUCCESS', 'COMPLETED'])) {
            $payment->status = $paymentStatusEnum::COMPLETED;
            $payment->paid_at = now();
        } elseif ($status === 'FAILED') {
            $payment->status = $paymentStatusEnum::FAILED;
            $payment->failed_at = now();
        } elseif (in_array($status, ['PENDING', 'PROCESSING'])) {
            $payment->status = $paymentStatusEnum::PROCESSING;
        }

        $payment->gateway_response = $data;
        $payment->save();

        return ['success' => true, 'data' => ['payment_id' => $payment->id, 'status' => $payment->status->value]];
    }

    /**
     * Handle booking callback.
     */
    protected function handleBookingCallback(array $data): array
    {
        // Handle booking-related callbacks
        return ['success' => true, 'data' => ['message' => 'Booking callback processed']];
    }

    /**
     * Handle trip callback.
     */
    protected function handleTripCallback(array $data): array
    {
        // Handle trip-related callbacks
        return ['success' => true, 'data' => ['message' => 'Trip callback processed']];
    }

    /**
     * Send callback HTTP request.
     */
    protected function sendCallbackRequest(Callback $callback): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($callback->endpoint, $callback->payload);

            return [
                'success' => $response->successful(),
                'data' => $response->json() ?? ['status_code' => $response->status()],
                'error' => $response->failed() ? $response->body() : null,
            ];
        } catch (\Exception $e) {
            Log::error('Callback HTTP request failed', [
                'callback_id' => $callback->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'error' => $e->getMessage(),
            ];
        }
    }
}