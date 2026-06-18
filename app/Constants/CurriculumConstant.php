<?php

namespace App\Constants;

class CurriculumConstant
{
    const K13    = 1;
    const KURMER = 2;
    const KTSP   = 3;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::K13    => 'Kurikulum 2013',
            self::KURMER => 'Kurikulum Merdeka',
            self::KTSP   => 'KTSP',
            default      => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::K13    => 'Kurikulum 2013',
            self::KURMER => 'Kurikulum Merdeka',
            self::KTSP   => 'KTSP',
        ];
    }
}
