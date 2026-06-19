<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $key = Str::lower($request->email) . '|' . $request->ip();

        // Rate limiting — max 5 percobaan per menit
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);

            $user = Auth::user();

            // Cek apakah akun aktif
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun kamu tidak aktif. Hubungi admin sekolah.']);
            }

            // Update last login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            $this->logLoginAttempt($request->email, $request->ip(), true);

            $request->session()->regenerate();

            // Cek harus ganti password
            if ($user->must_change_password) {
                return redirect()->route('auth.change-password');
            }

            // Tentukan sekolah aktif
            return $this->redirectAfterLogin($user);
        }

        RateLimiter::hit($key, 60);
        $this->logLoginAttempt($request->email, $request->ip(), false);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    protected function redirectAfterLogin($user)
    {
        // Super admin tidak terikat sekolah tertentu
        if ($user->hasRole('super_admin')) {
            return redirect()->route('dashboard');
        }

        $schoolCount = $user->userSchools()->count();

        if ($schoolCount === 1) {
            $userSchool = $user->userSchools()->first();
            session(['active_school_id' => $userSchool->school_id]);
            return redirect()->route('dashboard');
        }

        if ($schoolCount > 1) {
            return redirect()->route('select.school');
        }

        // Siswa / Orang tua — biasanya terhubung ke 1 sekolah via student/parent
        if ($user->student) {
            session(['active_school_id' => $user->student->school_id]);
            return redirect()->route('dashboard');
        }

        if ($user->studentParents()->exists()) {
            $studentParent = $user->studentParents()->with('student')->first();
            session(['active_school_id' => $studentParent->student->school_id]);
            return redirect()->route('dashboard');
        }

        return redirect()->route('dashboard');
    }

    protected function logLoginAttempt(string $email, string $ip, bool $success): void
    {
        LoginAttempt::create([
            'email'        => $email,
            'ip_address'   => $ip,
            'is_success'   => $success,
            'attempted_at' => now(),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }
}
