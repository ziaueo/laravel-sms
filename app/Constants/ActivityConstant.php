<?php

namespace App\Constants;

class ActivityConstant
{
    const LOGIN   = 1;
    const LOGOUT  = 2;
    const CREATE  = 3;
    const UPDATE  = 4;
    const DELETE  = 5;
    const APPROVE = 6;
    const REJECT  = 7;
    const EXPORT  = 8;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::LOGIN   => 'Login',
            self::LOGOUT  => 'Logout',
            self::CREATE  => 'Tambah Data',
            self::UPDATE  => 'Ubah Data',
            self::DELETE  => 'Hapus Data',
            self::APPROVE => 'Approve',
            self::REJECT  => 'Reject',
            self::EXPORT  => 'Export',
            default       => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::LOGIN   => 'Login',
            self::LOGOUT  => 'Logout',
            self::CREATE  => 'Tambah Data',
            self::UPDATE  => 'Ubah Data',
            self::DELETE  => 'Hapus Data',
            self::APPROVE => 'Approve',
            self::REJECT  => 'Reject',
            self::EXPORT  => 'Export',
        ];
    }
}
