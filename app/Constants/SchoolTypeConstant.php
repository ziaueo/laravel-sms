<?php

namespace App\Constants;

class SchoolTypeConstant
{
    const PAUD = 1;
    const TK   = 2;
    const SD   = 3;
    const SMP  = 4;
    const SMA  = 5;
    const SMK  = 6;

    /**
     * Apakah tipe sekolah ini menggunakan sistem jurusan/peminatan.
     */
    public static function hasMajors(int $schoolTypeId): bool
    {
        return in_array($schoolTypeId, [self::SMA, self::SMK]);
    }

    /**
     * Mulai tingkat berapa penjurusan berlaku.
     * Return null jika sekolah tidak punya sistem jurusan.
     */
    public static function majorStartsFromGradeOrder(int $schoolTypeId): ?int
    {
        return match($schoolTypeId) {
            self::SMA => 2, // Kelas 11 = urutan ke-2 (Kelas 10=1, 11=2, 12=3)
            self::SMK => 1, // Kelas 10 = urutan ke-1 (dari awal masuk)
            default   => null,
        };
    }

    public static function isElementary(int $schoolTypeId): bool
    {
        return in_array($schoolTypeId, [self::PAUD, self::TK, self::SD]);
    }
}
