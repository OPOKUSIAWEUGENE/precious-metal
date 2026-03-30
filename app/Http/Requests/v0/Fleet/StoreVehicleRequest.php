<?php

namespace App\Http\Requests\v0\Fleet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Fleets\Enums\VehicleStatus;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fleet_identifier' => 'required|string|unique:vehicles,fleet_identifier|max:255',
            'operator_id' => 'required|uuid|exists:operators,id',
            'type' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'status' => ['nullable', Rule::enum(VehicleStatus::class)],
            'created_by' => 'nullable|uuid',
            'updated_by' => 'nullable|uuid',
        ];
    }
}