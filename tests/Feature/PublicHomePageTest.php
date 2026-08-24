<?php

namespace Tests\Feature;

use App\Constants\StudentStatusConstant;
use App\Models\Banner;
use App\Models\Classroom;
use App\Models\Extracurricular;
use App\Models\GradeLevel;
use App\Models\School;
use App\Models\SchoolProfile;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomePageTest extends TestCase
{
    use RefreshDatabase;

    private function url(School $school): string
    {
        return route('public.home', $school->slug);
    }

    // ── HERO SLIDER ─────────────────────────────────────

    public function test_hero_merender_banner_yang_dipublikasikan(): void
    {
        $school = School::factory()->create();
        Banner::factory()->titled('Penerimaan Siswa Baru')->create([
            'school_id'   => $school->id,
            'button_text' => 'Daftar Sekarang',
            'button_url'  => 'https://contoh.test/daftar',
        ]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Penerimaan Siswa Baru')
            ->assertSee('Daftar Sekarang')
            ->assertSee('id="heroSlider"', false);
    }

    public function test_banner_belum_dipublikasikan_tidak_muncul(): void
    {
        $school = School::factory()->create();
        Banner::factory()->titled('Banner Tayang')->create(['school_id' => $school->id]);
        Banner::factory()->titled('Banner Draf')->unpublished()->create(['school_id' => $school->id]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Banner Tayang')
            ->assertDontSee('Banner Draf');
    }

    public function test_banner_urut_menurut_kolom_order(): void
    {
        $school = School::factory()->create();
        Banner::factory()->titled('Banner Kedua')->create(['school_id' => $school->id, 'order' => 2]);
        Banner::factory()->titled('Banner Pertama')->create(['school_id' => $school->id, 'order' => 1]);

        $content = $this->get($this->url($school))->assertOk()->getContent();

        $this->assertLessThan(
            strpos($content, 'Banner Kedua'),
            strpos($content, 'Banner Pertama'),
            'Banner dengan order lebih kecil harus dirender lebih dulu.'
        );
    }

    public function test_tanpa_banner_hero_jatuh_ke_gradien_bertagline(): void
    {
        $school = School::factory()->create();
        SchoolProfile::create(['school_id' => $school->id, 'tagline' => 'Unggul dan Berakhlak']);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Unggul dan Berakhlak')
            ->assertSee('phero-plain', false)
            ->assertDontSee('id="heroSlider"', false);
    }

    // ── STATISTIK ───────────────────────────────────────

    public function test_statistik_menghitung_siswa_guru_dan_ekskul(): void
    {
        $school = School::factory()->create();

        Student::factory()->count(3)->create(['school_id' => $school->id]);
        Student::factory()->status(StudentStatusConstant::ALUMNI)->create(['school_id' => $school->id]);

        Teacher::factory()->count(2)->create(['school_id' => $school->id]);
        Teacher::factory()->inactive()->create(['school_id' => $school->id]);

        Extracurricular::factory()->create(['school_id' => $school->id]);
        Extracurricular::factory()->inactive()->create(['school_id' => $school->id]);

        $content = $this->get($this->url($school))->assertOk()->getContent();

        // Alumni, guru nonaktif, dan ekskul nonaktif tidak ikut terhitung.
        $this->assertStringContainsString('<div class="pstat-num">3</div>', $content);
        $this->assertStringContainsString('<div class="pstat-num">2</div>', $content);
        $this->assertStringContainsString('<div class="pstat-num">1</div>', $content);
    }

    /**
     * classrooms terikat school_year_id. Menghitung semua barisnya akan
     * menjumlahkan kelas dari tahun-tahun sebelumnya — bug yang baru terlihat
     * setelah tahun ajaran berganti.
     */
    public function test_kelas_hanya_dihitung_dari_tahun_ajaran_aktif(): void
    {
        $school   = School::factory()->create();
        $grade    = GradeLevel::factory()->create(['school_id' => $school->id]);
        $tahunLlu = SchoolYear::factory()->create(['school_id' => $school->id]);
        $tahunIni = SchoolYear::factory()->active()->create(['school_id' => $school->id]);

        Classroom::factory()->count(4)->create([
            'school_id' => $school->id, 'school_year_id' => $tahunLlu->id, 'grade_level_id' => $grade->id,
        ]);
        Classroom::factory()->count(2)->create([
            'school_id' => $school->id, 'school_year_id' => $tahunIni->id, 'grade_level_id' => $grade->id,
        ]);

        $content = $this->get($this->url($school))->assertOk()->getContent();

        $this->assertStringContainsString('<div class="pstat-num">2</div>', $content);
        $this->assertStringNotContainsString('<div class="pstat-num">6</div>', $content);
    }

    public function test_statistik_disembunyikan_saat_semua_angkanya_nol(): void
    {
        $school = School::factory()->create();

        $this->get($this->url($school))
            ->assertOk()
            ->assertDontSee('pstat-num', false);
    }

    // ── VISI & MISI ─────────────────────────────────────

    public function test_visi_misi_muncul_saat_terisi(): void
    {
        $school = School::factory()->create();
        SchoolProfile::create([
            'school_id' => $school->id,
            'vision'    => 'Menjadi sekolah unggulan',
            'mission'   => "Membina akhlak mulia\nMeningkatkan mutu pembelajaran",
        ]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Visi')
            ->assertSee('Menjadi sekolah unggulan')
            ->assertSee('Membina akhlak mulia')
            ->assertSee('Meningkatkan mutu pembelajaran');
    }

    public function test_visi_misi_disembunyikan_saat_kosong(): void
    {
        $school = School::factory()->create();

        $this->get($this->url($school))
            ->assertOk()
            ->assertDontSee('pvm-card', false);
    }

    // ── EKSTRAKURIKULER ─────────────────────────────────

    public function test_ekstrakurikuler_aktif_muncul_yang_nonaktif_tidak(): void
    {
        $school = School::factory()->create();
        Extracurricular::factory()->named('Pramuka')->create(['school_id' => $school->id]);
        Extracurricular::factory()->named('Robotika')->inactive()->create(['school_id' => $school->id]);

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('Pramuka')
            ->assertDontSee('Robotika');
    }

    // ── NAVIGASI ────────────────────────────────────────

    /**
     * Sebelum redesain, navigasi hilang total di bawah 820px tanpa pengganti.
     */
    public function test_menu_mobile_hadir_di_halaman(): void
    {
        $school = School::factory()->create();

        $this->get($this->url($school))
            ->assertOk()
            ->assertSee('id="navPanel"', false)
            ->assertSee('data-nav-open', false)
            ->assertSee('data-nav-close', false);
    }
}
