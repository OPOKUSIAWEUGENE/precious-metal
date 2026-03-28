<?php

namespace App\Http\Requests\v0\Finances;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Finances\Enums\SettlementStatus;

class UpdateSettlementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'operator_id' => 'sometimes|uuid|exists:operators,id',
            'driver_profile_id' => 'nullable|uuid|exists:profiles,id',
            'period_start' => 'sometimes|date',
            'period_end' => 'sometimes|date|after_or_equal:period_start',
            'total_amount' => 'sometimes|numeric|min:0',
            'commission_amount' => 'sometimes|numeric|min:0',
            'net_amount' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::enum(SettlementStatus::class)],
            'payment_method' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:255',
            'processed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}