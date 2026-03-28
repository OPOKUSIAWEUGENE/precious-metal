<?php

namespace App\Http\Requests\v0\Trips;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Trips\Enums\TripStatus;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'route_id' => 'sometimes|uuid|exists:routes,id',
            'vehicle_id' => 'sometimes|uuid|exists:vehicles,id',
            'driver_profile_id' => 'sometimes|uuid|exists:profiles,id',
            'scheduled_at' => 'sometimes|date',
            'departure_stop_id' => 'sometimes|uuid|exists:route_stops,id',
            'arrival_stop_id' => 'sometimes|uuid|exists:route_stops,id',
            'status' => ['sometimes', Rule::enum(TripStatus::class)],
        ];
    }
}