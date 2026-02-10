<?php

namespace App\Http\Requests\Admin\Tour;

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

            'image' => ['nullable', 'string', 'max:255'],
            'gallery' => ['nullable', 'array'],

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
            'code.required' => 'Code is required',
            'code.unique' => 'Code already exists',
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'slug.required' => 'Slug is required',
            'slug.unique' => 'Slug already exists',

            'category_id.exists' => 'Category does not exist',
            'departure_location_id.required' => 'Departure location is required',
            'departure_location_id.exists' => 'Departure location does not exist',
            'destination_location_id.required' => 'Destination location is required',
            'destination_location_id.exists' => 'Destination location does not exist',
            'destination_location_id.different' => 'Destination location must be different from departure location',

            'duration_days.required' => 'Duration days is required',
            'duration_days.min' => 'Duration days must be at least 1',

            'price_adult.required' => 'Adult price is required',
            'price_adult.min' => 'Adult price must be greater than or equal to 0',

            'status.required' => 'Status is required',
            'status.enum' => 'Invalid tour status',
        ];
    }
}


