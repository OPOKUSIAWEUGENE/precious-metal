<?php

namespace App\Http\Requests\v0\Auth\Customer;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone_number' => 'required|string|max:20',
            'password' => 'required|string|min:8|max:255',
            'confirm_password' => 'required|string|min:8|max:255|same:password',
            'email' => 'nullable|email|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'resident_address' => 'nullable|string|max:500',
            'selfie' => 'nullable|string|max:255',
            'kyc' => 'nullable|array',
            'card_details' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'confirm_password.same' => 'The password and confirmation must match.',
        ];
    }
}
