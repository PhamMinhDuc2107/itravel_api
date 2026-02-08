<?php

namespace App\Http\Requests\Admin\BankAccount;

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
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:255'],
            'branch' => ['nullable', 'string', 'max:255'],
            'logo' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'qr_code' => [
                'nullable',
                'image',
                'mimes:' . UploadConstant::getImageMimesString(),
                'max:' . UploadConstant::IMAGE_MAX_SIZE,
            ],
            'position' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'bank_name.required' => 'Bank name is required',
            'account_number.required' => 'Account number is required',
            'account_holder.required' => 'Account holder is required',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be 0 or 1',
            'logo.image' => __('validation.custom.avatar.image'),
            'logo.mimes' => __('validation.custom.avatar.mimes'),
            'logo.max' => __('validation.custom.avatar.max'),
            'qr_code.image' => __('validation.custom.avatar.image'),
            'qr_code.mimes' => __('validation.custom.avatar.mimes'),
            'qr_code.max' => __('validation.custom.avatar.max'),
        ];
    }
}

