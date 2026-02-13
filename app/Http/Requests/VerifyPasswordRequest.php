<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route should be behind auth middleware if using $request->user()
        // Returning true lets Laravel handle the middleware protection
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['nullable', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must be at least 8 characters.',
            'email.email'       => 'Email must be valid.',
        ];
    }
}
