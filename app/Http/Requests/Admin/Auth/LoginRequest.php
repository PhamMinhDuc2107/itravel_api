<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => __('validation.email.required'),
            'email.email'       => __('validation.email.email'),
            'email.max'         => __('validation.email.max'),

            'password.required' => __('validation.password.required'),
            'password.min'      => __('validation.password.min'),
            'password.max'      => __('validation.password.max'),
        ];
    }
}
