<?php

namespace App\Http\Requests\v0\Routes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'start_stop_id' => 'sometimes|uuid|exists:route_stops,id',
            'end_stop_id' => 'sometimes|uuid|exists:route_stops,id',
            'status' => 'sometimes|in:ACTIVE,INACTIVE,ARCHIVED',
            'route_type' => 'sometimes|in:INTERCITY,INTRA_CITY,EXPRESS',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}