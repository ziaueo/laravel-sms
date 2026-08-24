<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicGalleryPageTest extends TestCase
{
    use RefreshDatabase;

    private function albumUrl(School $school, Gallery $album): string
    {
        return route('public.galeri.detail', [$school->slug, hid($album)]);
    }

    /**
     * Bug yang diperbaiki: kartu album di beranda semuanya menaut ke indeks
     * galeri, sehingga mengklik satu album justru membuka halaman berisi
     * seluruh album.
     */
    public function test_kartu_album_di_beranda_menaut_ke_album_masing_masing(): void
    {
        $school = School::factory()->create();
        $a = Gallery::factory()->titled('ALBUM A')->create(['school_id' => $school->id]);
        $b = Gallery::factory()->titled('ALBUM B')->create(['school_id' => $school->id]);

        $response = $this->get(route('public.home', $school->slug))->assertOk();

        $response->assertSee($this->albumUrl($school, $a), false);
        $response->assertSee($this->albumUrl($school, $b), false);
    }

    public function test_halaman_album_hanya_memuat_foto_album_itu(): void
    {
        $school = School::factory()->create();
        $a = Gallery::factory()->titled('ALBUM A')->create(['school_id' => $school->id]);
        $b = Gallery::factory()->titled('ALBUM B')->create(['school_id' => $school->id]);

        GalleryItem::factory()->captioned('Foto Album A')->create(['gallery_id' => $a->id]);
        GalleryItem::factory()->captioned('Foto Album B')->create(['gallery_id' => $b->id]);

        $this->get($this->albumUrl($school, $a))
            ->assertOk()
            ->assertSee('ALBUM A')
            ->assertSee('Foto Album A')
            ->assertDontSee('Foto Album B')
            ->assertDontSee('ALBUM B');
    }

    public function test_foto_di_halaman_album_bergrup_lightbox_album_itu(): void
    {
        $school = School::factory()->create();
        $album  = Gallery::factory()->create(['school_id' => $school->id]);
        GalleryItem::factory()->create(['gallery_id' => $album->id]);

        $this->get($this->albumUrl($school, $album))
            ->assertOk()
            ->assertSee('data-lightbox="galeri-' . hid($album) . '"', false);
    }

    public function test_halaman_galeri_menampilkan_kartu_album_bukan_tumpukan_foto(): void
    {
        $school = School::factory()->create();
        $album  = Gallery::factory()->titled('ALBUM A')->create(['school_id' => $school->id]);
        GalleryItem::factory()->captioned('Foto Album A')->create(['gallery_id' => $album->id]);

        $response = $this->get(route('public.galeri', $school->slug))->assertOk();

        $response->assertSee('ALBUM A');
        $response->assertSee('1 foto');
        $response->assertSee($this->albumUrl($school, $album), false);

        // Fotonya hanya muncul setelah masuk ke albumnya.
        $response->assertDontSee('Foto Album A');
        $response->assertDontSee('data-lightbox="galeri-', false);
    }

    public function test_album_sekolah_lain_menghasilkan_404(): void
    {
        $school = School::factory()->create();
        $lain   = School::factory()->create();
        $album  = Gallery::factory()->create(['school_id' => $lain->id]);

        $this->get($this->albumUrl($school, $album))->assertNotFound();
    }

    public function test_album_belum_dipublikasikan_menghasilkan_404(): void
    {
        $school = School::factory()->create();
        $album  = Gallery::factory()->unpublished()->create(['school_id' => $school->id]);

        $this->get($this->albumUrl($school, $album))->assertNotFound();
    }

    public function test_hashid_ngawur_menghasilkan_404(): void
    {
        $school = School::factory()->create();

        $this->get(route('public.galeri.detail', [$school->slug, 'bukan-hashid']))
            ->assertNotFound();
    }
}
