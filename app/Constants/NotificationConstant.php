<?php

namespace App\Constants;

class NotificationConstant
{
    const PENGUMUMAN = 1;
    const KOMENTAR   = 2;
    const ABSENSI    = 3;
    const NILAI      = 4;
    const SISTEM     = 5;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::PENGUMUMAN => 'Pengumuman',
            self::KOMENTAR   => 'Komentar',
            self::ABSENSI    => 'Absensi',
            self::NILAI      => 'Nilai',
            self::SISTEM     => 'Sistem',
            default          => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::PENGUMUMAN => 'Pengumuman',
            self::KOMENTAR   => 'Komentar',
            self::ABSENSI    => 'Absensi',
            self::NILAI      => 'Nilai',
            self::SISTEM     => 'Sistem',
        ];
    }
}
