<?php

namespace App\Enums;

use App\Models\AdminModel;

enum AuthUserTypeEnum: string
{
    case ADMIN   = 'admin';
    case USER    = 'user';
    case PARTNER = 'partner';


    public function model(): string
    {
        return match ($this) {
            self::ADMIN   => AdminModel::class,
            // self::USER    => \App\Models\UserModel::class,
            // self::PARTNER => \App\Models\Partner::class,
        };
    }

    public function guard(): string
    {
        return match ($this) {
            self::ADMIN   => 'admin',
            self::USER    => 'web',
            self::PARTNER => 'partner',
        };
    }
}
