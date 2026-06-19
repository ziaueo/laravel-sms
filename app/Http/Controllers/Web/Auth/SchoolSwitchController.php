<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolSwitchController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            $schools = \App\Models\School::where('is_active', true)->get();
        } else {
            $schools = $user->schools()->where('is_active', true)->get();
        }

        return view('auth.select-school', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        session(['active_school_id' => $request->school_id]);

        return redirect()->route('dashboard');
    }
}
