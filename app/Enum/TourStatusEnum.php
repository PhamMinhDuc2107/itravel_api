<?php

namespace App\Enum;

enum TourStatusEnum: int
{
    case Draft = 0;      // Draft
    case Published = 1;  // Published
    case Closed = 2;     // Closed
    case Hidden = 3;     // Hidden
    public function isDraft(): bool
    {
        return $this === self::Draft;
    }
    public function isPublished(): bool
    {
        return $this === self::Published;
    }
    public function isClosed(): bool
    {
        return $this === self::Closed;
    }
    public function isHidden(): bool
    {
        return $this === self::Hidden;
    }
}