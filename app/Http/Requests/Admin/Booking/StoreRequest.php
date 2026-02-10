<?php

namespace App\Http\Requests\Admin\Booking;

use App\Enum\BookingStatusEnum;
use App\Enum\PassengerTypeEnum;
use App\Enum\PaymentMethodEnum;
use App\Enum\PaymentStatusEnum;
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
            // User & Customer info
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_email' => ['required', 'email', 'max:255'],
            'note' => ['nullable', 'string'],

            // Financial
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],

            // Status
            'status' => [
                'required',
                'string',
                Rule::enum(BookingStatusEnum::class),
            ],
            'payment_status' => [
                'required',
                'string',
                Rule::enum(PaymentStatusEnum::class),
            ],
            'payment_method' => [
                'required',
                'string',
                Rule::enum(PaymentMethodEnum::class),
            ],
            'source' => ['nullable', 'string', 'max:50'],

            // Items
            'items' => ['required', 'array', 'min:1'],
            'items.*.productable_type' => ['nullable', 'string', 'max:255'],
            'items.*.productable_id' => ['nullable', 'integer'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_image' => ['nullable', 'string', 'max:500'],
            'items.*.product_code' => ['nullable', 'string', 'max:100'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.start_date' => ['nullable', 'date'],
            'items.*.end_date' => ['nullable', 'date', 'after_or_equal:items.*.start_date'],
            'items.*.options' => ['nullable', 'array'],

            // Passengers (nested under items)
            'items.*.passengers' => ['nullable', 'array'],
            'items.*.passengers.*.full_name' => ['required', 'string', 'max:255'],
            'items.*.passengers.*.dob' => ['nullable', 'date', 'before:today'],
            'items.*.passengers.*.gender' => ['nullable', 'string', 'in:male,female,other'],
            'items.*.passengers.*.phone' => ['nullable', 'string', 'max:50'],
            'items.*.passengers.*.passport_number' => ['nullable', 'string', 'max:50'],
            'items.*.passengers.*.passport_expiry' => ['nullable', 'date', 'after:today'],
            'items.*.passengers.*.nationality' => ['nullable', 'string', 'max:100'],
            'items.*.passengers.*.type' => [
                'required',
                'string',
                Rule::enum(PassengerTypeEnum::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => __('validation.custom.customer_name.required'),
            'customer_phone.required' => __('validation.custom.customer_phone.required'),
            'customer_email.required' => __('validation.custom.customer_email.required'),
            'customer_email.email' => __('validation.custom.customer_email.email'),

            'status.required' => __('validation.custom.status.required'),
            'status.enum' => __('validation.custom.status.enum'),
            'payment_status.required' => __('validation.custom.payment_status.required'),
            'payment_status.enum' => __('validation.custom.payment_status.enum'),
            'payment_method.required' => __('validation.custom.payment_method.required'),
            'payment_method.enum' => __('validation.custom.payment_method.enum'),

            'items.required' => 'Phải có ít nhất 1 sản phẩm.',
            'items.min' => 'Phải có ít nhất 1 sản phẩm.',
            'items.*.product_name.required' => 'Tên sản phẩm là bắt buộc.',
            'items.*.quantity.required' => 'Số lượng là bắt buộc.',
            'items.*.quantity.min' => 'Số lượng phải ít nhất là 1.',
            'items.*.price.required' => 'Giá là bắt buộc.',
            'items.*.price.min' => 'Giá phải lớn hơn hoặc bằng 0.',
            'items.*.end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',

            'items.*.passengers.*.full_name.required' => 'Họ tên hành khách là bắt buộc.',
            'items.*.passengers.*.dob.before' => 'Ngày sinh phải trước ngày hôm nay.',
            'items.*.passengers.*.passport_expiry.after' => 'Ngày hết hạn hộ chiếu phải sau ngày hôm nay.',
            'items.*.passengers.*.type.required' => 'Loại hành khách là bắt buộc.',
            'items.*.passengers.*.type.enum' => 'Loại hành khách không hợp lệ.',
        ];
    }
}

