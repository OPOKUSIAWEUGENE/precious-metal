<?php

namespace App\Http\Requests\v0\Routes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'status' => 'nullable|in:ACTIVE,INACTIVE,ARCHIVED',
            'route_type' => 'nullable|in:INTERCITY,INTRA_CITY,EXPRESS',
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