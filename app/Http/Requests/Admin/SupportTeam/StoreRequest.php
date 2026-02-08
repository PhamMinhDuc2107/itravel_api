<?php

namespace App\Http\Requests\Admin\SupportTeam;

use App\Constant\UploadConstant;
use App\Enum\SupportTeamGroupEnum;
use App\Enum\SupportTeamRoleEnum;
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
            'phone' => ['required', 'string', 'max:20'],
            'zalo' => ['nullable', 'string', 'max:255'],
            'avatar' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'role' => [
                'required',
                'string',
                Rule::enum(SupportTeamRoleEnum::class),
            ],
            'group' => [
                'required',
                'string',
                Rule::enum(SupportTeamGroupEnum::class),
            ],
            'position' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.name.required'),
            'name.max' => __('validation.custom.name.max'),
            'phone.required' => 'Phone is required',
            'role.required' => 'Role is required',
            'role.enum' => 'Invalid role',
            'group.required' => 'Group is required',
            'group.enum' => 'Invalid group',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be 0 or 1',
            'avatar.image' => __('validation.custom.avatar.image'),
            'avatar.mimes' => __('validation.custom.avatar.mimes'),
            'avatar.max' => __('validation.custom.avatar.max'),
        ];
    }
}

