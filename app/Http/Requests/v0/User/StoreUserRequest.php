<?php

namespace App\Http\Requests\v0\User;

use App\Modules\v0\Users\Enums\ProfileType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'social_login_provider' => 'nullable|string|max:50',
            'social_login_id' => 'nullable|string|max:255',
            'profile_img' => 'nullable|string|url',
            'active' => 'nullable|boolean',
            'profile' => 'nullable|array',
            'profile.profile_type' => [
                'nullable',
                'string',
                Rule::in(array_column(ProfileType::cases(), 'value')),
            ],
            'profile.role_id' => 'nullable|uuid|exists:roles,id',
            'profile.operator_id' => 'nullable|uuid|exists:operators,id',
            'profile.license' => 'nullable|string|max:255',
            'profile.active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'phone_number.max' => 'The phone number cannot exceed 20 characters.',
            'profile_img.url' => 'Please provide a valid URL for the profile image.',
            'profile.profile_type.in' => 'Invalid profile type. Valid types are: PLATFORM_OPERATOR, CLIENT_OPERATOR, PASSENGER, DRIVER.',
            'profile.role_id.exists' => 'The specified role does not exist.',
            'profile.operator_id.exists' => 'The specified operator does not exist.',
        ];
    }
}
