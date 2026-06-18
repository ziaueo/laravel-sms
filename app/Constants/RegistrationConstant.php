<?php

namespace App\Constants;

class RegistrationConstant
{
    const PENDING  = 1;
    const APPROVED = 2;
    const REJECTED = 3;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::PENDING  => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            default        => 'Tidak Diketahui',
        };
    }

    public static function getBadgeClass(int $value): string
    {
        return match($value) {
            self::PENDING  => 'badge-amber',
            self::APPROVED => 'badge-green',
            self::REJECTED => 'badge-red',
            default        => 'badge-amber',
        };
    }

    public static function getAll(): array
    {
        return [
            self::PENDING  => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        ];
    }
}
