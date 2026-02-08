<?php
namespace App\Enum;

enum SupportTeamRoleEnum: string
{
    case Consultant = 'consultant';
    case Manager = 'manager';
    case Admin = 'admin';
    public function isConsultant(): bool
    {
        return $this === self::Consultant;
    }
    public function isManager(): bool
    {
        return $this === self::Manager;
    }
    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}