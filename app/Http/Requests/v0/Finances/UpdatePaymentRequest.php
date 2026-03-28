<?php

namespace App\Http\Requests\v0\Finances;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'sometimes|uuid|exists:bookings,id',
            'passenger_profile_id' => 'sometimes|uuid|exists:profiles,id',
            'operator_id' => 'sometimes|uuid|exists:operators,id',
            'amount' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:3',
            'payment_method' => 'sometimes|string|in:CASH,CARD,MOBILE_MONEY,BANK_TRANSFER,WALLET,CREDIT',
            'status' => 'sometimes|string|in:PENDING,PROCESSING,COMPLETED,FAILED,REFUNDED,CANCELLED',
            'transaction_id' => 'sometimes|string|max:255',
            'external_reference' => 'sometimes|string|max:255',
            'gateway' => 'sometimes|string|max:100',
            'gateway_response' => 'sometimes|array',
            'metadata' => 'sometimes|array',
            'paid_at' => 'sometimes|date',
            'failed_at' => 'sometimes|date',
            'refunded_at' => 'sometimes|date',
            'notes' => 'sometimes|string',
        ];
    }
}