<?php
namespace App\Enum;

enum SupportTeamGroupEnum: string
{
    case General = 'general';
    case Domestic = 'domestic';
    case International = 'international';
    case Visa = 'visa';
    case Ticket = 'ticket';
    case Other = 'other';

    public function isGeneral(): bool
    {
        return $this === self::General;
    }
    public function isDomestic(): bool
    {
        return $this === self::Domestic;
    }
    public function isInternational(): bool
    {
        return $this === self::International;
    }
    public function isVisa(): bool
    {
        return $this === self::Visa;
    }
    public function isTicket(): bool
    {
        return $this === self::Ticket;
    }
}