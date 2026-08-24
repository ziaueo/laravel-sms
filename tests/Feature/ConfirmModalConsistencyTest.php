<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Seluruh konfirmasi memakai globalConfirmModal lewat atribut data-confirm.
 * Sebelumnya dua belas view memakai confirm() bawaan browser — dialog abu-abu
 * milik sistem operasi yang tampil beda dari sisa aplikasi.
 */
class ConfirmModalConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private function bladeFiles(): array
    {
        $files = [];

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($it as $file) {
            if (str_ends_with($file->getFilename(), '.blade.php')) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    public function test_tidak_ada_view_yang_memakai_confirm_bawaan_browser(): void
    {
        $offenders = [];

        foreach ($this->bladeFiles() as $path) {
            if (preg_match('/\bconfirm\s*\(/', (string) file_get_contents($path))) {
                $offenders[] = str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $path);
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "View berikut masih memakai confirm() bawaan browser:\n  " . implode("\n  ", $offenders)
        );
    }

    public function test_ampersand_di_data_confirm_sudah_dilolos(): void
    {
        $offenders = [];

        foreach ($this->bladeFiles() as $path) {
            // & yang bukan awal entity (&amp; &quot; dst) tidak sah di atribut HTML.
            if (preg_match('/data-confirm="[^"]*&(?![a-zA-Z]+;|#)/', (string) file_get_contents($path))) {
                $offenders[] = str_replace(resource_path('views') . DIRECTORY_SEPARATOR, '', $path);
            }
        }

        $this->assertSame([], $offenders, 'Ampersand mentah di atribut data-confirm: ' . implode(', ', $offenders));
    }

    public function test_halaman_verifikasi_pendaftaran_memakai_data_confirm(): void
    {
        Role::findOrCreate('super_admin', 'web');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $admin = User::factory()->create()->assignRole('super_admin');

        $response = $this->actingAs($admin)->get(route('registrations.index'))->assertOk();

        // Modal global tersedia di halaman, dan tidak ada dialog bawaan browser.
        $response->assertSee('id="globalConfirmModal"', false);
        $response->assertDontSee('onsubmit="return confirm', false);
    }
}
