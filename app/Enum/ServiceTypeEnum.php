<?php

namespace App\Enum;

enum ServiceTypeEnum: string
{
    case Tour = 'tour';
    case Hotel = 'hotel';
    case Visa = 'visa';
    case Flight = 'flight';
    case Combo = 'combo';
}