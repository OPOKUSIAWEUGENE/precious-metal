<?php

namespace App\Http\Requests\v0\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Enums\DriverStatus;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_type' => ['sometimes', Rule::enum(ProfileType::class)],
            'role_id' => 'nullable|uuid|exists:roles,id',
            'operator_id' => 'nullable|uuid|exists:operators,id',
            'user_id' => 'sometimes|uuid|exists:users,id',
            'active' => 'nullable|boolean',
            'current' => 'nullable|boolean',
            'license' => 'nullable|string|max:255',
            'driver_status' => ['nullable', Rule::enum(DriverStatus::class)],
            'created_by' => 'nullable|uuid',
            'updated_by' => 'nullable|uuid',
        ];
    }
}