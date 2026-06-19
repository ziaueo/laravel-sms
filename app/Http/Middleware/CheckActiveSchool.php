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
                return redirect()->route('select.school');
            }
        }

        return $next($request);
    }
}
