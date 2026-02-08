<?php
namespace App\Enum;

enum BlogStatusEnum: string 
{
    case Draft = 'draft';
    case Published = 'published';
    case Pending = 'pending';
    public function isDraft(): bool
    {
        return $this === self::Draft;
    }
    public function isPublished(): bool
    {
        return $this === self::Published;
    }
    public function isPending(): bool
    {
        return $this === self::Pending;
    }
}