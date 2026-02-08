<?php
namespace App\Enum;

enum ConsultationStatusEnum: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    public function label(): string
    {
        return match($this) {
            self::Pending => 'Chờ xử lý',
            self::Processing => 'Đang xử lý',
            self::Completed => 'Đã hoàn thành',
            self::Cancelled => 'Đã hủy',
        };
    }
    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Processing => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
