<?php

namespace App\Enum;

enum PaymentStatusEnum: string
{
    case Unpaid = 'unpaid';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Unpaid => 'Chưa thanh toán',
            self::PartiallyPaid => 'Đã đặt cọc',
            self::Paid => 'Đã thanh toán',
            self::Refunded => 'Đã hoàn tiền',
        };
    }
}