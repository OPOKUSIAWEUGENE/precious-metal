<?php

namespace App\Http\Requests\v0\Trips;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Trips\Enums\RouteStatus;
use App\Modules\v0\Trips\Enums\RouteType;

class StoreRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'start_stop_id' => 'required|uuid|exists:route_stops,id',
            'end_stop_id' => 'required|uuid|exists:route_stops,id',
            'status' => ['nullable', Rule::enum(RouteStatus::class)],
            'route_type' => ['required', Rule::enum(RouteType::class)],
        ];
    }
}