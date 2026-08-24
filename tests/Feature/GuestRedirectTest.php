<?php

namespace Tests\Feature;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tamu yang membuka halaman aplikasi harus dilempar ke halaman login.
 *
 * Sebelum perbaikan ini semuanya berbalas HTTP 500 dengan
 * "Route [login] not defined": Laravel mencari rute bernama `login` persis,
 * sedangkan proyek ini menamainya `auth.login`.
 */
class GuestRedirectTest extends TestCase
{
    use RefreshDatabase;

    public static function halamanTerlindungi(): array
    {
        return [
            'dashboard'       => ['/dashboard'],
            'siswa'           => ['/students'],
            'guru'            => ['/teachers'],
            'kelas'           => ['/classrooms'],
            'master akademik' => ['/master-data/akademik'],
            'sekolah'         => ['/master-data/schools'],
            'pengguna'        => ['/users'],
            'verifikasi'      => ['/registrations'],
            'pengaturan'      => ['/settings'],
            'notifikasi'      => ['/notifications'],
            'cms'             => ['/cms'],
            'ppdb'            => ['/ppdb'],
            'pengumuman'      => ['/announcements'],
            'rapot'           => ['/report-cards'],
            'penilaian'       => ['/scores'],
            'absensi'         => ['/attendances'],
            'jadwal'          => ['/schedules'],
            'ekstrakurikuler' => ['/extracurriculars'],
            'ganti sekolah'   => ['/select-school/list'],
            'ganti password'  => ['/auth/change-password'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('halamanTerlindungi')]
    public function test_tamu_dilempar_ke_halaman_login(string $path): void
    {
        $this->get($path)->assertRedirect(route('auth.login'));
    }

    public function test_tujuan_semula_diingat_supaya_bisa_dilanjutkan_setelah_login(): void
    {
        $this->get('/students')->assertRedirect(route('auth.login'));

        $this->assertSame(url('/students'), session('url.intended'));
    }

    // ── Yang harus tetap terbuka ────────────────────────

    public static function halamanTerbuka(): array
    {
        return [
            'login'           => ['/auth/login'],
            'registrasi'      => ['/auth/register'],
            'lupa password'   => ['/forgot-password'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('halamanTerbuka')]
    public function test_halaman_auth_tetap_bisa_dibuka_tamu(string $path): void
    {
        $this->get($path)->assertOk();
    }

    public function test_akar_mengarahkan_ke_login(): void
    {
        $this->get('/')->assertRedirect(route('auth.login'));
    }

    /**
     * Situs publik sekolah harus tetap terbuka untuk siapa pun — itu memang
     * halaman yang ditujukan bagi orang luar.
     */
    public function test_situs_publik_sekolah_tetap_terbuka(): void
    {
        $school = School::factory()->create();

        foreach (['', '/profil', '/guru', '/berita', '/galeri', '/kontak', '/ppdb'] as $sub) {
            $this->get('/s/' . $school->slug . $sub)
                ->assertOk();
        }
    }
}
