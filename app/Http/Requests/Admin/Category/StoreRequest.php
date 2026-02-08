<?php

namespace App\Http\Requests\Admin\Category;

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
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'position' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'slug.required' => 'Slug is required',
            'slug.unique' => 'Slug already exists',
            'parent_id.exists' => 'Parent category does not exist',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be 0 or 1',
        ];
    }
}

