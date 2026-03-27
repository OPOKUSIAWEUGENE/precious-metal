<?php

namespace App\Http\Requests\v0\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('id')),
                'max:255'
            ],
            'password' => 'sometimes|string|min:8|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'social_login_provider' => 'nullable|string|max:50',
            'social_login_id' => 'nullable|string|max:255',
            'profile_img' => 'nullable|url|max:255',
            'active' => 'nullable|boolean',
        ];
    }
}