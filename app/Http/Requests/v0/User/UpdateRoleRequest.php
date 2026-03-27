<?php

namespace App\Http\Requests\v0\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                Rule::unique('roles', 'name')->ignore($this->route('id')),
                'max:255'
            ],
            'description' => 'nullable|string|max:500',
        ];
    }
}