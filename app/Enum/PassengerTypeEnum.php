<?php
namespace App\Enum;

enum PassengerTypeEnum: string {
    case Adult = 'adult';
    case Child = 'child';
    case Infant = 'infant';
}