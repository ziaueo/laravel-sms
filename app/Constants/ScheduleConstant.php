<?php

namespace App\Constants;

class ScheduleConstant
{
    // Type jadwal
    const PELAJARAN       = 1;
    const ISTIRAHAT       = 2;
    const UPACARA         = 3;
    const EKSTRAKURIKULER = 4;

    // Hari
    const SENIN  = 1;
    const SELASA = 2;
    const RABU   = 3;
    const KAMIS  = 4;
    const JUMAT  = 5;
    const SABTU  = 6;

    public static function getTypeLabel(int $value): string
    {
        return match($value) {
            self::PELAJARAN       => 'Pelajaran',
            self::ISTIRAHAT       => 'Istirahat',
            self::UPACARA         => 'Upacara',
            self::EKSTRAKURIKULER => 'Ekstrakurikuler',
            default               => 'Tidak Diketahui',
        };
    }

    public static function getDayLabel(int $value): string
    {
        return match($value) {
            self::SENIN  => 'Senin',
            self::SELASA => 'Selasa',
            self::RABU   => 'Rabu',
            self::KAMIS  => 'Kamis',
            self::JUMAT  => 'Jumat',
            self::SABTU  => 'Sabtu',
            default      => 'Tidak Diketahui',
        };
    }

    public static function getAllType(): array
    {
        return [
            self::PELAJARAN       => 'Pelajaran',
            self::ISTIRAHAT       => 'Istirahat',
            self::UPACARA         => 'Upacara',
            self::EKSTRAKURIKULER => 'Ekstrakurikuler',
        ];
    }

    public static function getAllDay(): array
    {
        return [
            self::SENIN  => 'Senin',
            self::SELASA => 'Selasa',
            self::RABU   => 'Rabu',
            self::KAMIS  => 'Kamis',
            self::JUMAT  => 'Jumat',
            self::SABTU  => 'Sabtu',
        ];
    }
}
