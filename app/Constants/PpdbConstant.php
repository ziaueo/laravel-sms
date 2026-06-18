<?php

namespace App\Constants;

class PpdbConstant
{
    const PENDING    = 1;
    const VERIFIKASI = 2;
    const DITERIMA   = 3;
    const DITOLAK    = 4;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::PENDING    => 'Pending',
            self::VERIFIKASI => 'Verifikasi',
            self::DITERIMA   => 'Diterima',
            self::DITOLAK    => 'Ditolak',
            default          => 'Tidak Diketahui',
        };
    }

    public static function getBadgeClass(int $value): string
    {
        return match($value) {
            self::PENDING    => 'badge-amber',
            self::VERIFIKASI => 'badge-blue',
            self::DITERIMA   => 'badge-green',
            self::DITOLAK    => 'badge-red',
            default          => 'badge-amber',
        };
    }

    public static function getAll(): array
    {
        return [
            self::PENDING    => 'Pending',
            self::VERIFIKASI => 'Verifikasi',
            self::DITERIMA   => 'Diterima',
            self::DITOLAK    => 'Ditolak',
        ];
    }
}
