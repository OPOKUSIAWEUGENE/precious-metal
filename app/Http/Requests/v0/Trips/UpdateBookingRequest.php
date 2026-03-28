<?php

namespace App\Http\Requests\v0\Trips;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Trips\Enums\BookingStatus;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'sometimes|uuid|exists:trips,id',
            'passenger_profile_id' => 'sometimes|uuid|exists:profiles,id',
            'seat_count' => 'sometimes|integer|min:1',
            'status' => ['sometimes', Rule::enum(BookingStatus::class)],
        ];
    }
}