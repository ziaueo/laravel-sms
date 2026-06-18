<?php

namespace App\Constants;

class AttendanceConstant
{
    // Status
    const HADIR      = 1;
    const SAKIT      = 2;
    const IZIN       = 3;
    const ALPA       = 4;
    const DINAS_LUAR = 5;

    // Source
    const SOURCE_MESIN  = 1;
    const SOURCE_MANUAL = 2;

    public static function getStatusLabel(int $value): string
    {
        return match($value) {
            self::HADIR      => 'Hadir',
            self::SAKIT      => 'Sakit',
            self::IZIN       => 'Izin',
            self::ALPA       => 'Alpa',
            self::DINAS_LUAR => 'Dinas Luar',
            default          => 'Tidak Diketahui',
        };
    }

    public static function getStatusBadgeClass(int $value): string
    {
        return match($value) {
            self::HADIR      => 'badge-hadir',
            self::SAKIT      => 'badge-sakit',
            self::IZIN       => 'badge-izin',
            self::ALPA       => 'badge-alpa',
            self::DINAS_LUAR => 'badge-blue',
            default          => 'badge-green',
        };
    }

    public static function getAllStatus(): array
    {
        return [
            self::HADIR      => 'Hadir',
            self::SAKIT      => 'Sakit',
            self::IZIN       => 'Izin',
            self::ALPA       => 'Alpa',
            self::DINAS_LUAR => 'Dinas Luar',
        ];
    }

    public static function getSourceLabel(int $value): string
    {
        return match($value) {
            self::SOURCE_MESIN  => 'Mesin',
            self::SOURCE_MANUAL => 'Manual',
            default             => 'Tidak Diketahui',
        };
    }

    public static function getAllSource(): array
    {
        return [
            self::SOURCE_MESIN  => 'Mesin',
            self::SOURCE_MANUAL => 'Manual',
        ];
    }
}
