<?php

namespace App\Http\Requests\v0\User;

use App\Modules\v0\User\Enums\ProfileType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProfileRequest extends FormRequest
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
            'user_id' => 'required|uuid|exists:users,id',
            'profile_type' => [
                'required',
                'string',
                Rule::in(array_column(ProfileType::cases(), 'value')),
            ],
            'role_id' => 'nullable|uuid|exists:roles,id',
            'operator_id' => 'nullable|uuid|exists:operators,id',
            'active' => 'nullable|boolean',
            'current' => 'nullable|boolean',
            'license' => 'nullable|string|max:255',
            'driver_status' => 'nullable|string|in:AVAILABLE,ON_TRIP,OFF_DUTY,ON_BREAK,SUSPENDED',
            'created_by' => 'nullable|uuid',
            'updated_by' => 'nullable|uuid',
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
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The specified user does not exist.',
            'profile_type.required' => 'The profile type is required.',
            'profile_type.in' => 'Invalid profile type. Valid types are: PLATFORM_OPERATOR, CLIENT_OPERATOR, PASSENGER, DRIVER.',
            'role_id.exists' => 'The specified role does not exist.',
            'operator_id.exists' => 'The specified operator does not exist.',
            'driver_status.in' => 'Invalid driver status. Valid statuses are: AVAILABLE, ON_TRIP, OFF_DUTY, ON_BREAK, SUSPENDED.',
        ];
    }
}