<?php

namespace App\Http\Requests\v0\User;

use App\Traits\ApiResponse;
use App\Traits\Errors;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    use ApiResponse, Errors;

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            self::error(
                self::$badRequest,
                $validator->errors()->all(),
                400
            )
        );
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'profile_img' => 'nullable|string|url',
            'role' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'phone_number.max' => 'The phone number cannot exceed 20 characters.',
            'profile_img.url' => 'Please provide a valid URL for the profile image.',
        ];
    }
}
