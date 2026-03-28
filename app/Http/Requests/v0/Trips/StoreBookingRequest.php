<?php

namespace App\Http\Requests\v0\Trips;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Trips\Enums\BookingStatus;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|uuid|exists:trips,id',
            'passenger_profile_id' => 'required|uuid|exists:profiles,id',
            'seat_count' => 'nullable|integer|min:1',
            'status' => ['nullable', Rule::enum(BookingStatus::class)],
        ];
    }
}