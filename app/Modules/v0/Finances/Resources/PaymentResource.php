<?php

namespace App\Modules\v0\Finances\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'passenger_profile_id' => $this->passenger_profile_id,
            'operator_id' => $this->operator_id,
            'amount' => $this->amount,
            'currency' => $this->currency ?? 'GHS',
            'payment_method' => $this->payment_method?->value ?? $this->payment_method,
            'status' => $this->status?->value ?? $this->status,
            'transaction_id' => $this->transaction_id,
            'external_reference' => $this->external_reference,
            'gateway' => $this->gateway,
            'gateway_response' => $this->gateway_response,
            'metadata' => $this->metadata,
            'paid_at' => $this->paid_at?->toISOString(),
            'failed_at' => $this->failed_at?->toISOString(),
            'refunded_at' => $this->refunded_at?->toISOString(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}