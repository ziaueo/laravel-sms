<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContactPageTest extends TestCase
{
    use RefreshDatabase;

    private function url(School $school): string
    {
        return route('public.kontak', $school->slug);
    }

    public function test_tautan_pendek_jadi_kartu_yang_membuka_tab_baru(): void
    {
        $school = School::factory()->create(['address' => 'Jalan Merdeka 10']);
        SchoolProfile::create([
            'school_id'  => $school->id,
            'maps_embed' => 'https://maps.app.goo.gl/bGfjgCQrTPenPBFv6',
        ]);

        $response = $this->get($this->url($school))->assertOk();

        $response->assertSee('https://maps.app.goo.gl/bGfjgCQrTPenPBFv6', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('Buka di Google Maps');

        // Tautan pendek tidak bisa di-frame, jadi jangan dipaksakan jadi iframe.
        $response->assertDontSee('<iframe', false);
    }

    public function test_tanpa_maps_embed_tombol_dibangun_dari_alamat_sekolah(): void
    {
        $school = School::factory()->create(['address' => 'Jalan Merdeka 10']);
        SchoolProfile::create(['school_id' => $school->id]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=' . urlencode('Jalan Merdeka 10'), false)
            ->assertSee('Buka di Google Maps');
    }

    public function test_tanpa_maps_dan_tanpa_alamat_menampilkan_pesan_belum_tersedia(): void
    {
        $school = School::factory()->create(['address' => null]);
        SchoolProfile::create(['school_id' => $school->id]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Peta lokasi belum tersedia')
            ->assertDontSee('Buka di Google Maps');
    }

    public function test_embed_google_yang_sah_dirender_sebagai_iframe(): void
    {
        $school = School::factory()->create(['address' => 'Jalan Merdeka 10']);
        SchoolProfile::create([
            'school_id'  => $school->id,
            'maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!abc" width="600" height="450"></iframe>',
        ]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('https://www.google.com/maps/embed?pb=!1m18!abc', false)
            ->assertSee('<iframe', false)
            ->assertSee('Buka di Google Maps');
    }

    /**
     * Sebelum perbaikan, kontak.blade.php mengeluarkan maps_embed lewat {!! !!}.
     * Admin sekolah mana pun bisa menempelkan skrip yang lalu berjalan di
     * browser setiap pengunjung halaman publik sekolahnya.
     */
    public function test_isian_berbahaya_tidak_pernah_keluar_mentah(): void
    {
        $school = School::factory()->create(['address' => 'Jalan Merdeka 10']);
        SchoolProfile::create([
            'school_id'  => $school->id,
            'maps_embed' => '<script>alert("xss")</script>',
        ]);

        $response = $this->get($this->url($school))->assertOk();

        $response->assertDontSee('<script>alert', false);
        $response->assertDontSee('alert("xss")', false);
    }

    public function test_iframe_dari_domain_selain_google_ditolak(): void
    {
        $school = School::factory()->create(['address' => 'Jalan Merdeka 10']);
        SchoolProfile::create([
            'school_id'  => $school->id,
            'maps_embed' => '<iframe src="https://situs-jahat.test/pelacak"></iframe>',
        ]);

        $response = $this->get($this->url($school))->assertOk();

        $response->assertDontSee('situs-jahat.test', false);
        $response->assertDontSee('<iframe', false);
    }
}
