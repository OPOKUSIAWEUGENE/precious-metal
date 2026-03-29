<?php

namespace App\Http\Requests\v0\Callbacks;

use Illuminate\Foundation\Http\FormRequest;

class SendOutgoingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'endpoint' => 'required|url|max:500',
            'payload' => 'required|array',
            'related_type' => 'nullable|string|max:100',
            'related_id' => 'nullable|uuid',
        ];
    }
}