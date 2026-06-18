<?php

namespace App\Constants;

class GalleryConstant
{
    const FOTO  = 1;
    const VIDEO = 2;

    public static function getLabel(int $value): string
    {
        return match($value) {
            self::FOTO  => 'Foto',
            self::VIDEO => 'Video',
            default     => 'Tidak Diketahui',
        };
    }

    public static function getAll(): array
    {
        return [
            self::FOTO  => 'Foto',
            self::VIDEO => 'Video',
        ];
    }
}
