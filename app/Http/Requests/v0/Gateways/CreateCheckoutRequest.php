<?php

namespace App\Http\Requests\v0\Gateways;

use Illuminate\Foundation\Http\FormRequest;

class CreateCheckoutRequest extends FormRequest
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
            'amount' => 'required|numeric|min:0.01',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ];
    }
}