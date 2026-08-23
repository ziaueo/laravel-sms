<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolSwitchController extends Controller
{
    /**
     * Batas baris daftar sekolah. Super admin bisa punya ratusan sekolah;
     * sisanya ditemukan lewat kotak pencarian, bukan lewat gulir panjang.
     */
    private const LIST_LIMIT = 50;

    public function index(): View
    {
        $schools = $this->accessibleSchools()
            ->with('schoolType')
            ->orderBy('name')
            ->get();

        return view('auth.select-school', compact('schools'));
    }

    /**
     * Potongan HTML daftar sekolah untuk modal ganti sekolah.
     * Sengaja mengembalikan view, bukan JSON, supaya markup kartu sekolah
     * tetap hidup di Blade seperti sisa proyek ini.
     */
    public function list(Request $request): View
    {
        $request->validate([
            'q' => 'nullable|string|max:100',
        ]);

        $query = $this->accessibleSchools()->with('schoolType');

        if ($q = trim((string) $request->query('q'))) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        $schools = $query->orderBy('name')->limit(self::LIST_LIMIT)->get();

        return view('components.school-switcher-items', [
            'schools'   => $schools,
            'isLimited' => $schools->count() === self::LIST_LIMIT,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        $allowed = $this->accessibleSchools()
            ->whereKey($request->school_id)
            ->exists();

        if (! $allowed) {
            abort(403, 'Kamu tidak memiliki akses ke sekolah ini.');
        }

        session(['active_school_id' => (int) $request->school_id]);

        return redirect()->route('dashboard');
    }

    /**
     * Sekolah aktif yang boleh diakses user saat ini.
     * Super admin melihat semua sekolah; user lain hanya sekolah miliknya.
     *
     * Memakai subquery ke user_schools, bukan relasi belongsToMany, karena
     * tabel itu unik pada (user_id, school_id, role) — user yang memegang dua
     * peran di sekolah yang sama akan muncul dua kali lewat relasi.
     */
    private function accessibleSchools(): Builder
    {
        $user  = Auth::user();
        $query = School::query()->where('is_active', true);

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        return $query->whereIn('id', $user->userSchools()->select('school_id'));
    }
}
