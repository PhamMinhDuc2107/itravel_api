<?php

namespace App\Enum;

enum ActiveStateEnum: int
{
    case Active = 1;
    case InActive = 0;

    public static function isActiveValue(int $value): bool
    {
        return self::tryFrom((int) $value) === self::Active;
    }


    public static function isInactiveValue(int $value): bool
    {
        return self::tryFrom((int) $value) === self::InActive;
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
