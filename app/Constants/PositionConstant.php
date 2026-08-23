<?php

namespace App\Constants;

class PositionConstant
{
    const GURU     = 1;
    const STAFF    = 2;
    const PIMPINAN = 3;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::GURU     => 'Guru',
            self::STAFF    => 'Staff',
            self::PIMPINAN => 'Pimpinan',
            default        => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::PIMPINAN => 'Pimpinan',
            self::GURU     => 'Guru',
            self::STAFF    => 'Staff',
        ];
    }
}
