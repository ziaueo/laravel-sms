<?php

namespace App\Constants;

class ParentRelationConstant
{
    const AYAH = 1;
    const IBU  = 2;
    const WALI = 3;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::AYAH => 'Ayah',
            self::IBU  => 'Ibu',
            self::WALI => 'Wali',
            default    => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::AYAH => 'Ayah',
            self::IBU  => 'Ibu',
            self::WALI => 'Wali',
        ];
    }
}
