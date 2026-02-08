<?php

namespace App\Enum;

enum BookingStatusEnum: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Declined = 'declined';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Chờ xử lý',
            self::Confirmed => 'Đã xác nhận',
            self::Processing => 'Đang thực hiện',
            self::Completed => 'Hoàn thành',
            self::Cancelled => 'Đã hủy',
            self::Declined => 'Đã từ chối',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Processing => 'primary',
            self::Completed => 'success',
            self::Cancelled, self::Declined => 'danger',
        };
    }
}