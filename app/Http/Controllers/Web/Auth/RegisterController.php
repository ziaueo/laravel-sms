<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\UserRegistration;
use App\Models\User;
use App\Constants\RoleConstant;
use App\Constants\RegistrationConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        return view('auth.register', compact('schools'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'password'  => ['required', 'confirmed', Password::min(8)],
            'role'      => 'required|integer|in:' . RoleConstant::SISWA . ',' . RoleConstant::ORANG_TUA,
            'school_id' => 'required|exists:schools,id',
            'identifier'=> 'required|string|max:50', // NIS/NISN siswa, atau NIS anak (ortu)
        ], [
            'role.in' => 'Hanya siswa atau orang tua yang dapat mendaftar mandiri.',
        ]);

        // Cek email belum dipakai di users / registrasi pending
        if (User::where('email', $request->email)->exists()) {
            return back()->withInput()->with('error', 'Email sudah terdaftar. Silakan login.');
        }
        if (UserRegistration::where('email', $request->email)->where('status', RegistrationConstant::PENDING)->exists()) {
            return back()->withInput()->with('error', 'Pendaftaran dengan email ini sedang menunggu verifikasi.');
        }

        UserRegistration::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'school_id' => $request->school_id,
            'data'      => ['identifier' => $request->identifier],
            'status'    => RegistrationConstant::PENDING,
        ]);

        return redirect()->route('auth.login')
            ->with('success', 'Pendaftaran berhasil dikirim. Akun akan aktif setelah diverifikasi admin sekolah.');
    }
}
