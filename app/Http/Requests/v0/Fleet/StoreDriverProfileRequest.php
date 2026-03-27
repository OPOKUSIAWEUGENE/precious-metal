<?php

namespace App\Http\Requests\v0\Fleet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\User\Enums\DriverStatus;

class StoreDriverProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'operator_id' => 'required|uuid|exists:operators,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'license' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::enum(DriverStatus::class)],
            'vehicle_identifiers' => 'nullable|array',
            'vehicle_identifiers.*' => 'string|max:255',
            'created_by' => 'nullable|uuid',
            'updated_by' => 'nullable|uuid',
        ];
    }
}