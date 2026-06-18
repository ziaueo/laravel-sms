<?php

namespace App\Constants;

class GenderConstant
{
    const LAKI_LAKI  = 1;
    const PEREMPUAN  = 2;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::LAKI_LAKI => 'Laki-laki',
            self::PEREMPUAN => 'Perempuan',
            default         => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::LAKI_LAKI => 'Laki-laki',
            self::PEREMPUAN => 'Perempuan',
        ];
    }
}
