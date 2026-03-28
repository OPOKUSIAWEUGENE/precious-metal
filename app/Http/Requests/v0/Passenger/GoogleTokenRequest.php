<?php

namespace App\Http\Requests\v0\Passenger;

use Illuminate\Foundation\Http\FormRequest;

class GoogleTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_token' => 'required|string',
        ];
    }
}