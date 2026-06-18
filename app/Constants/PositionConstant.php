<?php

namespace App\Constants;

class PositionConstant
{
    const GURU  = 1;
    const STAFF = 2;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::GURU  => 'Guru',
            self::STAFF => 'Staff',
            default     => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::GURU  => 'Guru',
            self::STAFF => 'Staff',
        ];
    }
}
