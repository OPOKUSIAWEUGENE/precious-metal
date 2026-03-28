<?php

namespace App\Http\Requests\v0\Trips;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Trips\Enums\TripStatus;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'route_id' => 'required|uuid|exists:routes,id',
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'driver_profile_id' => 'required|uuid|exists:profiles,id',
            'scheduled_at' => 'required|date',
            'departure_stop_id' => 'required|uuid|exists:route_stops,id',
            'arrival_stop_id' => 'required|uuid|exists:route_stops,id',
            'status' => ['nullable', Rule::enum(TripStatus::class)],
        ];
    }
}