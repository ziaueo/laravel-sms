<?php

namespace App\Constants;

class MutationConstant
{
    // Type
    const MASUK  = 1;
    const KELUAR = 2;

    // Status
    const PENDING   = 1;
    const DISETUJUI = 2;
    const DITOLAK   = 3;

    public static function getTypeLabel(int $value): string
    {
        return match($value) {
            self::MASUK  => 'Masuk',
            self::KELUAR => 'Keluar',
            default      => 'Tidak Diketahui',
        };
    }

    public static function getStatusLabel(int $value): string
    {
        return match($value) {
            self::PENDING   => 'Pending',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK   => 'Ditolak',
            default         => 'Tidak Diketahui',
        };
    }

    public static function getAllType(): array
    {
        return [
            self::MASUK  => 'Masuk',
            self::KELUAR => 'Keluar',
        ];
    }

    public static function getAllStatus(): array
    {
        return [
            self::PENDING   => 'Pending',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK   => 'Ditolak',
        ];
    }
}
