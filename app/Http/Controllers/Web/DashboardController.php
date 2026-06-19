<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Belum pilih sekolah aktif & bukan super admin & punya >1 sekolah
        if (!session('active_school_id') && !$user->hasRole('super_admin')) {
            $schoolCount = $user->userSchools()->count();
            if ($schoolCount > 1) {
                return redirect()->route('select.school');
            }
        }

        return view('dashboard.index');
    }
}
