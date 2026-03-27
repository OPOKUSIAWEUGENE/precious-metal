<?php

namespace App\Http\Requests\v0\Fleet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Fleet\Enums\VehicleStatus;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fleet_identifier' => [
                'sometimes',
                'string',
                Rule::unique('vehicles', 'fleet_identifier')->ignore($this->route('id')),
                'max:255'
            ],
            'operator_id' => 'sometimes|uuid|exists:operators,id',
            'type' => 'sometimes|string|max:255',
            'plate_number' => 'sometimes|string|max:20',
            'capacity' => 'sometimes|integer|min:1',
            'status' => ['sometimes', Rule::enum(VehicleStatus::class)],
            'created_by' => 'nullable|uuid',
            'updated_by' => 'nullable|uuid',
        ];
    }
}