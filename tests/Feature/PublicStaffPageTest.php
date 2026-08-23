<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicStaffPageTest extends TestCase
{
    use RefreshDatabase;

    private function url(School $school): string
    {
        return route('public.guru', $school->slug);
    }

    public function test_menampilkan_pegawai_aktif(): void
    {
        $school = School::factory()->create();
        Teacher::factory()->named('Budi Santoso')->create([
            'school_id'   => $school->id,
            'position_id' => Position::factory()->guru('Guru Kelas')->create()->id,
        ]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Guru Kelas');
    }

    public function test_pegawai_nonaktif_tidak_muncul(): void
    {
        $school = School::factory()->create();
        Teacher::factory()->named('Budi Santoso')->create(['school_id' => $school->id]);
        Teacher::factory()->named('Siti Aminah')->inactive()->create(['school_id' => $school->id]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Aminah');
    }

    public function test_pegawai_sekolah_lain_tidak_muncul(): void
    {
        $school = School::factory()->create();
        $lain   = School::factory()->create();

        Teacher::factory()->named('Budi Santoso')->create(['school_id' => $school->id]);
        Teacher::factory()->named('Siti Aminah')->create(['school_id' => $lain->id]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Aminah');
    }

    public function test_kepala_sekolah_ditonjolkan_dan_tidak_diulang(): void
    {
        $school = School::factory()->create();

        Teacher::factory()->named('Ahmad Hidayat')->create([
            'school_id'   => $school->id,
            'position_id' => Position::factory()->pimpinan('Kepala Sekolah', 1)->create()->id,
        ]);
        Teacher::factory()->named('Rina Wijaya')->create([
            'school_id'   => $school->id,
            'position_id' => Position::factory()->pimpinan('Wakil Kepala Sekolah', 2)->create()->id,
        ]);

        $response = $this->get($this->url($school))->assertOk();
        $content  = $response->getContent();

        // Kepala sekolah tampil di blok utama, bukan ikut lagi di daftar kelompok.
        $this->assertStringContainsString('lead-name', $content);
        $this->assertSame(1, substr_count($content, 'Ahmad Hidayat'));

        // Wakilnya tetap muncul, di seksi Pimpinan Sekolah.
        $response->assertSee('Pimpinan Sekolah');
        $response->assertSee('Rina Wijaya');
    }

    public function test_pegawai_tanpa_jabatan_tetap_muncul(): void
    {
        $school = School::factory()->create();
        Teacher::factory()->named('Tono Prasetyo')->create([
            'school_id'   => $school->id,
            'position_id' => null,
        ]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Tono Prasetyo')
            ->assertSee('Lainnya');
    }

    public function test_nip_ditampilkan(): void
    {
        $school = School::factory()->create();
        Teacher::factory()->named('Budi Santoso')->create([
            'school_id' => $school->id,
            'nip'       => '196512121990031007',
        ]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('196512121990031007');
    }

    public function test_data_pribadi_tidak_ikut_terbawa(): void
    {
        $school = School::factory()->create();
        Teacher::factory()->named('Budi Santoso')->create([
            'school_id'  => $school->id,
            'phone'      => '081200000999',
            'email'      => 'rahasia.guru@contoh.test',
            'address'    => 'Jalan Rahasia Nomor Tujuh',
            'birth_date' => '1980-07-15',
        ]);

        $response = $this->get($this->url($school))->assertOk();

        $response->assertSee('Budi Santoso');
        $response->assertDontSee('081200000999');
        $response->assertDontSee('rahasia.guru@contoh.test');
        $response->assertDontSee('Jalan Rahasia Nomor Tujuh');
        $response->assertDontSee('1980-07-15');
    }

    public function test_halaman_kosong_saat_belum_ada_pegawai(): void
    {
        $school = School::factory()->create();

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Data belum tersedia');
    }

    public function test_sekolah_nonaktif_menghasilkan_404(): void
    {
        $school = School::factory()->inactive()->create();

        $this->get($this->url($school))->assertNotFound();
    }
}
