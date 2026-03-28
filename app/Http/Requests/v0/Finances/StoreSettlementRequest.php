<?php

namespace App\Http\Requests\v0\Finances;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Finances\Enums\SettlementStatus;

class StoreSettlementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'operator_id' => 'required|uuid|exists:operators,id',
            'driver_profile_id' => 'nullable|uuid|exists:profiles,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'total_amount' => 'required|numeric|min:0',
            'commission_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::enum(SettlementStatus::class)],
            'payment_method' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}