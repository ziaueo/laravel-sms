<?php

namespace App\Constants;

class DeviceLogConstant
{
    const CHECK_IN  = 1;
    const CHECK_OUT = 2;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::CHECK_IN  => 'Check In',
            self::CHECK_OUT => 'Check Out',
            default         => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::CHECK_IN  => 'Check In',
            self::CHECK_OUT => 'Check Out',
        ];
    }
}
