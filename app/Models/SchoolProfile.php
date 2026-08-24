<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'tagline', 'description', 'vision',
        'mission', 'history', 'principal_name', 'principal_photo',
        'founded_year', 'facebook_url', 'instagram_url',
        'youtube_url', 'tiktok_url', 'maps_embed',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Sumber iframe peta, atau null kalau tidak ada yang layak di-frame.
     *
     * Isian maps_embed bebas: admin bisa menempel potongan <iframe>, tautan
     * pendek maps.app.goo.gl, atau apa saja. Yang dikembalikan di sini hanya
     * URL-nya, bukan HTML dari admin — iframe-nya dibangun sendiri oleh view
     * supaya isian admin tidak pernah keluar mentah ke halaman publik.
     */
    public function getMapsEmbedSrcAttribute(): ?string
    {
        $value = trim((string) $this->maps_embed);

        if ($value === '') {
            return null;
        }

        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $value, $m)) {
            $src = html_entity_decode($m[1]);
        } elseif (preg_match('#^https?://#i', $value)) {
            $src = $value;
        } else {
            return null;
        }

        // Hanya URL embed resmi Google yang boleh di-frame. Tautan pendek
        // maps.app.goo.gl sengaja tidak lolos: Google menolak halamannya
        // di-frame, jadi hasilnya cuma kotak kosong.
        return preg_match('#^https://(www\.)?google\.[a-z.]+/maps/embed#i', $src) ? $src : null;
    }

    /**
     * Tujuan tombol "Buka di Google Maps".
     *
     * Tautan yang diisi admin didahulukan karena itu penanda lokasi mereka
     * sendiri; kalau tidak ada, dibangun dari alamat sekolah.
     */
    public function getMapsLinkAttribute(): ?string
    {
        $value = trim((string) $this->maps_embed);

        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        $address = trim((string) $this->school?->address);

        return $address !== ''
            ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address)
            : null;
    }
}
