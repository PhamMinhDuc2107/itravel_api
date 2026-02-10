<?php

namespace App\Http\Requests\Admin\Amenity;

use App\Constant\UploadConstant;
use App\Enum\ActiveStateEnum;
use App\Enum\AmenityTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('amenities', 'code')->ignore($id),
            ],
            'icon' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'type' => [
                'required',
                'string',
                Rule::enum(AmenityTypeEnum::class),
            ],
            'position' => ['nullable', 'integer', 'min:0'],
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
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'code.required' => __('validation.custom.code.required'),
            'code.unique' => __('validation.custom.code.unique'),
            'type.required' => __('validation.custom.type.required'),
            'type.enum' => __('validation.custom.type.enum'),
            'status.required' => __('validation.custom.status.required'),
            'status.enum' => __('validation.custom.status.enum'),

            'icon.image' => __('validation.custom.avatar.image'),
            'icon.mimes' => __('validation.custom.avatar.mimes'),
            'icon.max' => __('validation.custom.avatar.max'),
        ];
    }
}


