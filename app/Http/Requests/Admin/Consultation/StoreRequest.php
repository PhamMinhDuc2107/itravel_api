<?php

namespace App\Http\Requests\Admin\Consultation;

use App\Enum\ConsultationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'productable_type' => ['nullable', 'string', 'max:255'],
            'productable_id' => ['nullable', 'integer'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
            'message' => ['nullable', 'string'],
            'status' => [
                'required',
                'string',
                Rule::enum(ConsultationStatusEnum::class),
            ],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'staff_notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'phone.required' => 'Phone is required',
            'email.email' => 'Email must be a valid email address',
            'status.required' => 'Status is required',
            'status.enum' => 'Invalid status',
            'user_id.exists' => 'User does not exist',
        ];
    }
}

