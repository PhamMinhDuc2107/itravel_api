<?php

namespace App\Http\Requests\Admin\Admin;

use App\Constant\UploadConstant;
use App\Enum\ActiveStateEnum;
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
        $id = $this->route('id') ?? $this->route('admin');

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($id)
            ],

            'phone' => ['nullable', 'string', 'min:9', 'max:15'],

            'password' => ['nullable', 'string', 'min:8'],

            'status' => [
                'required',
                Rule::enum(ActiveStateEnum::class),
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max'      => __('validation.custom.name.max'),

            'email.email'    => __('validation.custom.email.email'),
            'email.unique'   => __('validation.custom.email.unique'),

            'password.min'      => __('validation.custom.password.min'),

            'avatar.image' => __('validation.custom.avatar.image'),
            'avatar.mimes' => __('validation.custom.avatar.mimes'),
            'avatar.max'   => __('validation.custom.avatar.max'),

            'status.enum' => __('validation.custom.status.enum'),
        ];
    }
}
