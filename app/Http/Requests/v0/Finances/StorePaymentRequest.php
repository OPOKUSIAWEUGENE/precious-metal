<?php

namespace App\Http\Requests\v0\Finances;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|uuid|exists:bookings,id',
            'passenger_profile_id' => 'required|uuid|exists:profiles,id',
            'operator_id' => 'nullable|uuid|exists:operators,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'payment_method' => 'required|string|in:CASH,CARD,MOBILE_MONEY,BANK_TRANSFER,WALLET,CREDIT',
            'transaction_id' => 'nullable|string|max:255',
            'external_reference' => 'nullable|string|max:255',
            'gateway' => 'nullable|string|max:100',
            'gateway_response' => 'nullable|array',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string',
        ];
    }
}