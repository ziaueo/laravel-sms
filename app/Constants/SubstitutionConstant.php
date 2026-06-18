<?php

namespace App\Constants;

class SubstitutionConstant
{
    const GANTI_GURU  = 1;
    const GANTI_MAPEL = 2;
    const LIBUR       = 3;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::GANTI_GURU  => 'Ganti Guru',
            self::GANTI_MAPEL => 'Ganti Mapel',
            self::LIBUR       => 'Libur',
            default           => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::GANTI_GURU  => 'Ganti Guru',
            self::GANTI_MAPEL => 'Ganti Mapel',
            self::LIBUR       => 'Libur',
        ];
    }
}
