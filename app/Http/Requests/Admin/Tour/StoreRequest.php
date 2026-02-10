<?php

namespace App\Http\Requests\Admin\Tour;

use App\Constant\UploadConstant;
use App\Enum\TourStatusEnum;
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
            'code' => ['required', 'string', 'max:50', 'unique:tours,code'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:tours,slug'],

            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'departure_location_id' => ['required', 'integer', 'exists:locations,id'],
            'destination_location_id' => ['required', 'integer', 'exists:locations,id', 'different:departure_location_id'],

            'duration_days' => ['required', 'integer', 'min:1'],
            'duration_nights' => ['nullable', 'integer', 'min:0'],

            'is_recurring' => ['nullable', 'integer', 'in:0,1'],
            'recurring_days' => ['nullable', 'array'],
            'recurring_days.*' => ['integer', 'between:0,6'],

            'price_adult' => ['required', 'numeric', 'min:0'],
            'price_child' => ['nullable', 'numeric', 'min:0'],
            'price_infant' => ['nullable', 'numeric', 'min:0'],

            'excerpt' => ['nullable', 'string'],
            'overview' => ['nullable', 'string'],
            'policy' => ['nullable', 'string'],
            'included' => ['nullable', 'string'],
            'excluded' => ['nullable', 'string'],

            'image' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => [
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],

            'view_count' => ['nullable', 'integer', 'min:0'],
            'position' => ['nullable', 'integer', 'min:0'],

            'status' => [
                'required',
                'integer',
                Rule::enum(TourStatusEnum::class),
            ],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
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
            'gallery.*.image' => __('validation.custom.avatar.image'),
            'gallery.*.mimes' => __('validation.custom.avatar.mimes'),
            'gallery.*.max' => __('validation.custom.avatar.max'),
        ];
    }
}


