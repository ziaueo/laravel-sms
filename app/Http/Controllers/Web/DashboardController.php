<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Kondisi "belum pilih sekolah" ditangani sepenuhnya oleh middleware
        // CheckActiveSchool, yang menandai halaman ini agar modal pemilih
        // sekolah terkunci terbuka.
        return view('dashboard.index');
    }
}
