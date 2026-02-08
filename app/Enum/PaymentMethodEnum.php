<?php

namespace App\Enum;

enum PaymentMethodEnum: string
{
    case COD = 'cod';
    case BankTransfer = 'bank_transfer';
    case VNPay = 'vnpay';
    case Momo = 'momo';
    case CreditCard = 'credit_card';

    public function label(): string
    {
        return match($this) {
            self::COD => 'Tiền mặt',
            self::BankTransfer => 'Chuyển khoản',
            self::VNPay => 'VNPay',
            self::Momo => 'Ví MoMo',
            self::CreditCard => 'Thẻ tín dụng',
        };
    }
}