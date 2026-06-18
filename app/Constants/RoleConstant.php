<?php

namespace App\Constants;

class RoleConstant
{
    const SUPER_ADMIN    = 1;
    const KEPALA_SEKOLAH = 2;
    const GURU           = 3;
    const STAFF          = 4;
    const SISWA          = 5;
    const ORANG_TUA      = 6;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::SUPER_ADMIN    => 'Super Admin',
            self::KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::GURU           => 'Guru',
            self::STAFF          => 'Staff',
            self::SISWA          => 'Siswa',
            self::ORANG_TUA      => 'Orang Tua',
            default              => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::SUPER_ADMIN    => 'Super Admin',
            self::KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::GURU           => 'Guru',
            self::STAFF          => 'Staff',
            self::SISWA          => 'Siswa',
            self::ORANG_TUA      => 'Orang Tua',
        ];
    }

    public static function getSpatieRole(int $value): string
    {
        return match($value) {
            self::SUPER_ADMIN    => 'super_admin',
            self::KEPALA_SEKOLAH => 'kepala_sekolah',
            self::GURU           => 'guru',
            self::STAFF          => 'staff',
            self::SISWA          => 'siswa',
            self::ORANG_TUA      => 'orang_tua',
            default              => 'siswa',
        };
    }
}
