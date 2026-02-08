<?php
namespace App\Enum;

enum TourDepartureStatusEnum: string
{
    case Available = 'available';
    case SoldOut = 'sold_out';
    case Closed = 'closed';
    public function isAvailable(): bool
    {
        return $this === self::Available;
    }
    public function isSoldOut(): bool
    {
        return $this === self::SoldOut;
    }
    public function isClosed(): bool
    {
        return $this === self::Closed;
    }
}