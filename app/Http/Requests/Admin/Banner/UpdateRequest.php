<?php

namespace App\Http\Requests\Admin\Banner;

use App\Constant\UploadConstant;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'image' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'mobile_image' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'link' => ['nullable', 'string', 'max:500'],
            'target' => ['nullable', 'string', 'in:_self,_blank'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'integer', 'in:0,1'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'image.image' => __('validation.custom.avatar.image'),
            'image.mimes' => __('validation.custom.avatar.mimes'),
            'image.max' => __('validation.custom.avatar.max'),
            'mobile_image.image' => __('validation.custom.avatar.image'),
            'mobile_image.mimes' => __('validation.custom.avatar.mimes'),
            'mobile_image.max' => __('validation.custom.avatar.max'),
            'status.required' => 'Status is required',
            'status.in' => 'Status must be 0 or 1',
            'end_at.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }
}

