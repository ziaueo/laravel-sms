<?php

namespace App\Constants;

class RecapTypeConstant
{
    const SISWA = 1;
    const GURU  = 2;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::SISWA => 'Siswa',
            self::GURU  => 'Guru',
            default     => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::SISWA => 'Siswa',
            self::GURU  => 'Guru',
        ];
    }
}
