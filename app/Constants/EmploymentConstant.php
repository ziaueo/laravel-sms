<?php

namespace App\Constants;

class EmploymentConstant
{
    const PNS     = 1;
    const HONORER = 2;
    const TETAP   = 3;
    const KONTRAK = 4;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::PNS     => 'PNS',
            self::HONORER => 'Honorer',
            self::TETAP   => 'Tetap',
            self::KONTRAK => 'Kontrak',
            default       => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::PNS     => 'PNS',
            self::HONORER => 'Honorer',
            self::TETAP   => 'Tetap',
            self::KONTRAK => 'Kontrak',
        ];
    }
}
