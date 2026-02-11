<?php

namespace App\Http\Requests\Admin\Booking;

use Illuminate\Foundation\Http\FormRequest;

class ExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'status' => ['nullable', 'string'],
            'payment_status' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_from.date' => 'Ngày bắt đầu không hợp lệ.',
            'date_to.date' => 'Ngày kết thúc không hợp lệ.',
            'date_to.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'limit.integer' => 'Số lượng phải là số nguyên.',
            'limit.min' => 'Số lượng phải ít nhất là 1.',
            'limit.max' => 'Số lượng tối đa là 10000.',
        ];
    }
}

