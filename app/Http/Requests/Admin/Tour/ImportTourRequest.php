<?php

namespace App\Http\Requests\Admin\Tour;

use Illuminate\Foundation\Http\FormRequest;

class ImportTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240', // 10MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => __('validation.custom.file.required'),
            'file.file' => __('validation.custom.file.file'),
            'file.mimes' => __('validation.custom.file.mimes'),
            'file.max' => __('validation.custom.file.max'),
        ];
    }
}
