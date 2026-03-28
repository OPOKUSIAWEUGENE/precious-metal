<?php

namespace App\Http\Requests\v0\Finances;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Finances\Enums\CommissionStatus;

class StoreCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|uuid|exists:trips,id',
            'operator_id' => 'required|uuid|exists:operators,id',
            'driver_profile_id' => 'nullable|uuid|exists:profiles,id',
            'amount' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0|max:1',
            'gross_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::enum(CommissionStatus::class)],
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}