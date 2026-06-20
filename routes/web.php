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
            Route::get('/trash', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'trash'])->name('trash');
            Route::patch('/{id}/restore', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [\App\Http\Controllers\Web\SuperAdmin\UserController::class, 'forceDelete'])->name('force-delete');
        });

        // ── MASTER DATA: SEKOLAH ────────────────────────────────
        Route::prefix('master-data/schools')->name('master.schools.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'store'])->name('store');
            Route::put('/{school}', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'update'])->name('update');
            Route::patch('/{school}/toggle-active', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'toggleActive'])->name('toggle-active');
            Route::delete('/{school}', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'destroy'])->name('destroy');
            Route::get('/trash', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'trash'])->name('trash');
            Route::patch('/{id}/restore', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'forceDelete'])->name('force-delete');
        });

        // Redirect master.index ke master.schools.index (untuk sidebar)
        Route::get('/master-data', function () {
            return redirect()->route('master.schools.index');
        })->name('master.index');
    });

});
