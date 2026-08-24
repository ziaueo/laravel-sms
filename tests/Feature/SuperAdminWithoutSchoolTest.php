<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * CheckActiveSchool melepas super admin tanpa mengisi active_school_id, jadi
 * setiap controller sekolah harus tahan menghadapi active_school() bernilai
 * null. MasterDataController dan ClassroomController dulu tidak menjaganya
 * dan meledak dengan "Attempt to read property id on null".
 */
class SuperAdminWithoutSchoolTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        Role::findOrCreate('super_admin', 'web');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return User::factory()->create()->assignRole('super_admin');
    }

    public static function halamanSekolah(): array
    {
        return [
            'kelas'            => ['classrooms.index'],
            'tambah kelas'     => ['classrooms.create'],
            'master akademik'  => ['master.index'],
            'siswa'            => ['students.index'],
            'guru'             => ['teachers.index'],
            'jadwal'           => ['schedules.index'],
            'absensi'          => ['attendances.index'],
            'penilaian'        => ['scores.index'],
            'rapot'            => ['report-cards.index'],
            'pengumuman'       => ['announcements.index'],
            'ppdb'             => ['ppdb.index'],
            'cms'              => ['cms.index'],
            'ekstrakurikuler'  => ['extracurriculars.index'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('halamanSekolah')]
    public function test_tidak_meledak_saat_sekolah_belum_dipilih(string $routeName): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route($routeName))
            ->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_tetap_terbuka_untuk_super_admin_tanpa_sekolah(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('dashboard'))
            ->assertOk();
    }
}
