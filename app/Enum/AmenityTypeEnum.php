<?php
namespace App\Enum;

enum AmenityTypeEnum: string
{
    case General = 'general';
    case Room = 'room';
    case Bathroom = 'bathroom';
    case Dining = 'dining';
    case Entertainment = 'entertainment';
    case Other = 'other';
}