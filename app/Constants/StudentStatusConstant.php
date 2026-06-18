<?php

namespace App\Constants;

class StudentStatusConstant
{
    const AKTIF   = 1;
    const ALUMNI  = 2;
    const PINDAH  = 3;
    const KELUAR  = 4;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::AKTIF  => 'Aktif',
            self::ALUMNI => 'Alumni',
            self::PINDAH => 'Pindah',
            self::KELUAR => 'Keluar',
            default      => 'Tidak Diketahui',
        };
    }

    public static function getBadgeClass(int $value): string
    {
        return match($value) {
            self::AKTIF  => 'badge-green',
            self::ALUMNI => 'badge-blue',
            self::PINDAH => 'badge-amber',
            self::KELUAR => 'badge-red',
            default      => 'badge-green',
        };
    }

    public static function getAll(): array
    {
        return [
            self::AKTIF  => 'Aktif',
            self::ALUMNI => 'Alumni',
            self::PINDAH => 'Pindah',
            self::KELUAR => 'Keluar',
        ];
    }
}
