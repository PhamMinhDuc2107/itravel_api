<?php

namespace App\Http\Requests\Admin\Location;

use App\Constant\UploadConstant;
use App\Enum\ActiveStateEnum;
use App\Enum\LocationTypeEnum;
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
            'slug' => ['required', 'string', 'max:255', 'unique:locations,slug'],
            'parent_id' => ['nullable', 'integer', 'exists:locations,id'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'image' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'type' => [
                'required',
                'string',
                Rule::enum(LocationTypeEnum::class),
            ],
            'display_home' => ['nullable', 'integer', 'in:0,1'],
            'is_feature' => ['nullable', 'integer', 'in:0,1'],
            'is_departure' => ['nullable', 'integer', 'in:0,1'],
            'is_destination' => ['nullable', 'integer', 'in:0,1'],
            'position' => ['nullable', 'integer', 'min:0'],
            'status' => [
                'required',
                'string',
                'in:0,1',
            ],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'slug.required' => 'Slug is required',
            'slug.unique' => 'Slug already exists',
            'parent_id.exists' => 'Parent location does not exist',
            'type.required' => 'Type is required',
            'type.enum' => 'Invalid location type',
            'status.required' => 'Status is required',
            'status.enum' => 'Invalid status',
            'image.image' => __('validation.custom.avatar.image'),
            'image.mimes' => __('validation.custom.avatar.mimes'),
            'image.max' => __('validation.custom.avatar.max'),
        ];
    }
}

