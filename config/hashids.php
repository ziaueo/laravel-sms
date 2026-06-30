<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hashid URL Obfuscation
    |--------------------------------------------------------------------------
    |
    | Dipakai oleh helper hashid_encode() / hashid_decode() untuk menyamarkan
    | ID numerik pada URL. Salt WAJIB dirahasiakan (server-side). Jika salt
    | berubah, semua URL ber-hash lama akan tidak valid (data tetap aman,
    | hanya bookmark/link lama yang berubah).
    |
    | "length" = panjang minimum string hasil encode.
    |
    */

    'salt' => env('HASHIDS_SALT', env('APP_KEY', 'laravel-sms-hashid')),

    'length' => (int) env('HASHIDS_LENGTH', 12),

    'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',

    /*
    | Koneksi default untuk paket vinkla/hashids (tidak dipakai langsung oleh
    | helper, tapi disediakan agar service provider paket tetap valid).
    */
    'default' => 'main',

    'connections' => [
        'main' => [
            'salt'     => env('HASHIDS_SALT', env('APP_KEY', 'laravel-sms-hashid')),
            'length'   => (int) env('HASHIDS_LENGTH', 12),
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],
    ],

];
