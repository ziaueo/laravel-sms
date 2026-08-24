<?php

namespace Tests\Feature;

use App\Constants\RoleConstant;
use App\Models\School;
use App\Models\User;
use App\Models\UserSchool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Alur "belum pilih sekolah" setelah login. Sebelumnya user dilempar ke
 * halaman /select-school; sekarang halaman itu tidak ada lagi dan
 * pemilihannya lewat modal terkunci di atas dashboard.
 */
class SchoolPickerFlowTest extends TestCase
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

    private function userWithSchools(int $count): User
    {
        $user = User::factory()->create();

        foreach (School::factory()->count($count)->create() as $school) {
            $this->attach($user, $school);
        }

        return $user;
    }

    /**
     * Halaman lamanya dihapus, tapi URL-nya dipertahankan sebagai pengalih
     * supaya bookmark atau tab lama tidak berujung di halaman error.
     */
    public function test_url_select_school_lama_dialihkan_ke_dashboard(): void
    {
        $user = $this->userWithSchools(2);

        $this->actingAs($user)->get('/select-school')
            ->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_mengunci_modal_saat_sekolah_belum_dipilih(): void
    {
        $user = $this->userWithSchools(2);

        $response = $this->actingAs($user)->get(route('dashboard'))->assertOk();

        $response->assertSee('data-locked', false);
        $response->assertSee('modal-backdrop show', false);
        $response->assertSee('Pilih Sekolah');

        // Terkunci berarti tidak ada jalan menutupnya.
        $response->assertDontSee('data-ss-close', false);
    }

    public function test_halaman_lain_dilempar_ke_dashboard_bukan_dirender_tanpa_sekolah(): void
    {
        $user = $this->userWithSchools(2);

        $this->actingAs($user)->get(route('students.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_setelah_memilih_sekolah_modal_tidak_lagi_terkunci(): void
    {
        $user   = $this->userWithSchools(2);
        $school = $user->userSchools()->first()->school;

        $this->actingAs($user)->post('/select-school', ['school_id' => $school->id])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('active_school_id', $school->id);

        $response = $this->actingAs($user)->get(route('dashboard'))->assertOk();

        $response->assertDontSee('data-locked', false);
        $response->assertSee('data-ss-close', false);
    }

    public function test_user_dengan_satu_sekolah_langsung_dipilihkan_tanpa_modal(): void
    {
        $user = $this->userWithSchools(1);

        $response = $this->actingAs($user)->get(route('dashboard'))->assertOk();

        $response->assertDontSee('data-locked', false);
        // Satu sekolah berarti tidak ada yang bisa diganti — modal tidak dirender.
        $response->assertDontSee('id="schoolSwitcherModal"', false);
        $this->assertNotNull(session('active_school_id'));
    }

    public function test_user_tanpa_sekolah_ditolak(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
    }
}
