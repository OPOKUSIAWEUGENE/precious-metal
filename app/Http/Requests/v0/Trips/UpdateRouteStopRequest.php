<?php

namespace App\Http\Requests\v0\Trips;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Trips\Enums\RouteStatus;
use App\Modules\v0\Trips\Enums\RouteType;

class UpdateRouteStopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'description' => 'nullable|string|max:500',
        ];
    }
}