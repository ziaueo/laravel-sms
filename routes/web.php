<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\SchoolSwitchController;
use App\Http\Controllers\Web\DashboardController;

// ── PUBLIC ROUTES ──────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('auth.login');
});

// ── AUTH ROUTES ─────────────────────────────────────────
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// ── AUTHENTICATED ROUTES ────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Select School
    Route::get('/select-school', [SchoolSwitchController::class, 'index'])->name('select.school');
    Route::post('/select-school', [SchoolSwitchController::class, 'store']);

    // Dashboard
    Route::middleware(['check.school.access'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── PLACEHOLDER ROUTES (sementara, akan diisi controller asli nanti) ──
        $placeholder = function () {
            return '<div style="font-family:sans-serif;padding:40px;text-align:center;">
                <h2>🚧 Halaman ini sedang dalam pengembangan</h2>
                <p>Modul akan segera tersedia.</p>
                <a href="' . route('dashboard') . '">← Kembali ke Dashboard</a>
            </div>';
        };

        Route::get('/students', $placeholder)->name('students.index');
        Route::get('/teachers', $placeholder)->name('teachers.index');
        Route::get('/classrooms', $placeholder)->name('classrooms.index');
        Route::get('/schedules', $placeholder)->name('schedules.index');
        Route::get('/attendances', $placeholder)->name('attendances.index');
        Route::get('/scores', $placeholder)->name('scores.index');
        Route::get('/report-cards', $placeholder)->name('report-cards.index');
        Route::get('/announcements', $placeholder)->name('announcements.index');
        Route::get('/ppdb', $placeholder)->name('ppdb.index');
        Route::get('/cms', $placeholder)->name('cms.index');
        Route::get('/master-data', $placeholder)->name('master.index');
        Route::get('/settings', $placeholder)->name('settings.index');
        Route::get('/notifications', $placeholder)->name('notifications.index');

        // ── USER MANAGEMENT ────────────────────────────────────
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'store'])->name('store');
            Route::put('/{user}', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'update'])->name('update');
            Route::patch('/{user}/toggle-active', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'toggleActive'])->name('toggle-active');
            Route::patch('/{user}/reset-password', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'resetPassword'])->name('reset-password');
            Route::delete('/{user}', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'destroy'])->name('destroy');
        });
    });

});
