<?php

namespace App\Http\Requests\Admin\Hotel;

use App\Constant\UploadConstant;
use App\Enum\ActiveStateEnum;
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
            'slug' => ['required', 'string', 'max:255', 'unique:hotels,slug'],

            'hotel_type_id' => ['required', 'integer', 'exists:hotel_types,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],

            'address' => ['nullable', 'string', 'max:500'],

            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],

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

            'star_rating' => ['nullable', 'integer', 'between:0,5'],
            'price_from' => ['nullable', 'numeric', 'min:0'],

            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'policies' => ['nullable', 'string'],

            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],

            'check_in_time' => ['nullable', 'string', 'max:20'],
            'check_out_time' => ['nullable', 'string', 'max:20'],

            'is_featured' => ['nullable', 'integer', 'in:0,1'],
            'view_count' => ['nullable', 'integer', 'min:0'],

            'status' => [
                'required',
                'integer',
                Rule::enum(ActiveStateEnum::class),
            ],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],

            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'slug.required' => 'Slug is required',
            'slug.unique' => 'Slug already exists',

            'hotel_type_id.required' => 'Hotel type is required',
            'hotel_type_id.exists' => 'Hotel type does not exist',
            'location_id.required' => 'Location is required',
            'location_id.exists' => 'Location does not exist',

            'status.required' => 'Status is required',
            'status.enum' => 'Invalid status',

            'amenity_ids.*.exists' => 'Amenity does not exist',

            'image.image' => __('validation.custom.avatar.image'),
            'image.mimes' => __('validation.custom.avatar.mimes'),
            'image.max' => __('validation.custom.avatar.max'),
            'gallery.*.image' => __('validation.custom.avatar.image'),
            'gallery.*.mimes' => __('validation.custom.avatar.mimes'),
            'gallery.*.max' => __('validation.custom.avatar.max'),
        ];
    }
}


