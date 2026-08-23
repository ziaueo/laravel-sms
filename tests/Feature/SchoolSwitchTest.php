<?php

namespace Tests\Feature;

use App\Constants\RoleConstant;
use App\Models\School;
use App\Models\User;
use App\Models\UserSchool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SchoolSwitchTest extends TestCase
{
    use RefreshDatabase;

    private function attach(User $user, School $school): void
    {
        UserSchool::create([
            'user_id'   => $user->id,
            'school_id' => $school->id,
            'role'      => RoleConstant::GURU,
        ]);
    }

    private function superAdmin(): User
    {
        Role::findOrCreate('super_admin', 'web');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return User::factory()->create()->assignRole('super_admin');
    }

    // ── Daftar sekolah ──────────────────────────────────

    public function test_daftar_hanya_memuat_sekolah_milik_user(): void
    {
        $user = User::factory()->create();
        $this->attach($user, School::factory()->named('SDN Mekarsari')->create());
        School::factory()->named('SMPN Cibadak')->create();

        $this->actingAs($user)
            ->get(route('select.school.list'))
            ->assertOk()
            ->assertSee('SDN Mekarsari')
            ->assertDontSee('SMPN Cibadak');
    }

    public function test_super_admin_melihat_semua_sekolah_aktif(): void
    {
        School::factory()->named('SDN Mekarsari')->create();
        School::factory()->named('SMPN Cibadak')->create();

        $this->actingAs($this->superAdmin())
            ->get(route('select.school.list'))
            ->assertOk()
            ->assertSee('SDN Mekarsari')
            ->assertSee('SMPN Cibadak');
    }

    public function test_sekolah_nonaktif_tidak_muncul_di_daftar(): void
    {
        $user = User::factory()->create();
        $this->attach($user, School::factory()->named('SDN Mekarsari')->create());
        $this->attach($user, School::factory()->named('SMPN Cibadak')->inactive()->create());

        $this->actingAs($user)
            ->get(route('select.school.list'))
            ->assertOk()
            ->assertSee('SDN Mekarsari')
            ->assertDontSee('SMPN Cibadak');
    }

    public function test_pencarian_menyaring_berdasarkan_nama(): void
    {
        $user = User::factory()->create();
        $this->attach($user, School::factory()->named('SDN Mekarsari')->create());
        $this->attach($user, School::factory()->named('SMPN Cibadak')->create());

        $this->actingAs($user)
            ->get(route('select.school.list', ['q' => 'mekar']))
            ->assertOk()
            ->assertSee('SDN Mekarsari')
            ->assertDontSee('SMPN Cibadak');
    }

    // ── Perpindahan sekolah ─────────────────────────────

    public function test_bisa_pindah_ke_sekolah_miliknya(): void
    {
        $user   = User::factory()->create();
        $school = School::factory()->create();
        $this->attach($user, $school);

        $this->actingAs($user)
            ->post('/select-school', ['school_id' => $school->id])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('active_school_id', $school->id);
    }

    public function test_tidak_bisa_pindah_ke_sekolah_yang_bukan_haknya(): void
    {
        $user  = User::factory()->create();
        $this->attach($user, School::factory()->create());
        $lain  = School::factory()->create();

        $response = $this->actingAs($user)
            ->post('/select-school', ['school_id' => $lain->id]);

        $response->assertForbidden();
        $response->assertSessionMissing('active_school_id');
    }

    public function test_tidak_bisa_pindah_ke_sekolah_nonaktif(): void
    {
        $user   = User::factory()->create();
        $school = School::factory()->inactive()->create();
        $this->attach($user, $school);

        $this->actingAs($user)
            ->post('/select-school', ['school_id' => $school->id])
            ->assertForbidden();
    }
}
