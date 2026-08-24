<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSchool
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('auth.login');
        }

        // Super admin tidak wajib punya active school untuk akses tertentu
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        if (!session('active_school_id')) {
            $schoolCount = $user->userSchools()->count();

            if ($schoolCount === 0) {
                abort(403, 'Kamu tidak memiliki akses ke sekolah manapun.');
            }

            if ($schoolCount === 1) {
                $userSchool = $user->userSchools()->first();
                session(['active_school_id' => $userSchool->school_id]);
            } else {
                // Pemilih sekolah berupa modal, dan modal butuh halaman induk.
                // Dashboard memang dibangun tahan tanpa sekolah aktif; halaman
                // lain akan menjalankan query dengan school_id null, jadi
                // semuanya diarahkan ke dashboard lebih dulu.
                if ($request->routeIs('dashboard')) {
                    view()->share('lockSchoolPicker', true);

                    return $next($request);
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
