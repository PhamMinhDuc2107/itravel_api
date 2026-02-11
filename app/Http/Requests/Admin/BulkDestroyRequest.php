<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => __('validation.custom.ids.required'),
            'ids.array' => __('validation.custom.ids.array'),
            'ids.min' => __('validation.custom.ids.min'),
            'ids.*.integer' => __('validation.custom.ids.integer'),
        ];
    }
}
