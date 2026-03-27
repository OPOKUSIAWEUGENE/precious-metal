<?php

namespace App\Http\Requests\v0\Fleet;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => 'required|uuid|exists:profiles,id',
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
        ];
    }
}