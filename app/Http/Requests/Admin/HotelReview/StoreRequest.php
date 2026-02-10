<?php

namespace App\Http\Requests\Admin\HotelReview;

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
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:255'],
            'status' => [
                'required',
                'integer',
                Rule::enum(ActiveStateEnum::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_id.required' => 'Hotel is required',
            'hotel_id.exists' => 'Hotel does not exist',
            'name.required' => __('validation.custom.name.required'),
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'rating.required' => 'Rating is required',
            'rating.between' => 'Rating must be between 1 and 5',
            'status.required' => 'Status is required',
            'status.enum' => 'Invalid status',
        ];
    }
}


