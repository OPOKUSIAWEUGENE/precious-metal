<?php

namespace App\Http\Requests\v0\Finances;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Finances\Enums\CommissionStatus;

class UpdateCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'sometimes|uuid|exists:trips,id',
            'operator_id' => 'sometimes|uuid|exists:operators,id',
            'driver_profile_id' => 'nullable|uuid|exists:profiles,id',
            'amount' => 'sometimes|numeric|min:0',
            'rate' => 'sometimes|numeric|min:0|max:1',
            'gross_amount' => 'sometimes|numeric|min:0',
            'net_amount' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::enum(CommissionStatus::class)],
            'period_start' => 'sometimes|date',
            'period_end' => 'sometimes|date|after_or_equal:period_start',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}