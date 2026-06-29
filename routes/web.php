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

        // ── PLACEHOLDER ROUTES ──────────────────────────────
        $placeholder = function () {
            return '<div style="font-family:sans-serif;padding:40px;text-align:center;">
                <h2>🚧 Halaman ini sedang dalam pengembangan</h2>
                <p>Modul akan segera tersedia.</p>
                <a href="' . route('dashboard') . '">← Kembali ke Dashboard</a>
            </div>';
        };

        Route::get('/cms', $placeholder)->name('cms.index');
        Route::get('/settings', $placeholder)->name('settings.index');
        Route::get('/notifications', $placeholder)->name('notifications.index');

        // ── USER MANAGEMENT ─────────────────────────────────
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

        // ── MASTER DATA ──────────────────────────────────────
        Route::prefix('master-data')->name('master.')->group(function () {

            // Redirect /master-data ke akademik
            Route::get('/', function () {
                return redirect()->route('master.index');
            });

            // 1 halaman 5 tab (Tahun Ajaran, Tingkat Kelas, Mapel, KKM)
            Route::get('/akademik', [\App\Http\Controllers\Web\School\MasterDataController::class, 'index'])->name('index');

            // ── Tahun Ajaran ──
            Route::prefix('school-years')->name('school-years.')->group(function () {
                Route::post('/', [\App\Http\Controllers\Web\School\MasterDataController::class, 'storeSchoolYear'])->name('store');
                Route::put('/{schoolYear}', [\App\Http\Controllers\Web\School\MasterDataController::class, 'updateSchoolYear'])->name('update');
                Route::patch('/{schoolYear}/set-active', [\App\Http\Controllers\Web\School\MasterDataController::class, 'setActiveSchoolYear'])->name('set-active');
                Route::delete('/{schoolYear}', [\App\Http\Controllers\Web\School\MasterDataController::class, 'destroySchoolYear'])->name('destroy');
            });

            // ── Tingkat Kelas ──
            Route::prefix('grade-levels')->name('grade-levels.')->group(function () {
                Route::post('/', [\App\Http\Controllers\Web\School\MasterDataController::class, 'storeGradeLevel'])->name('store');
                Route::put('/{gradeLevel}', [\App\Http\Controllers\Web\School\MasterDataController::class, 'updateGradeLevel'])->name('update');
                Route::delete('/{gradeLevel}', [\App\Http\Controllers\Web\School\MasterDataController::class, 'destroyGradeLevel'])->name('destroy');
            });

            // ── Mata Pelajaran ──
            Route::prefix('subjects')->name('subjects.')->group(function () {
                Route::post('/', [\App\Http\Controllers\Web\School\MasterDataController::class, 'storeSubject'])->name('store');
                Route::put('/{subject}', [\App\Http\Controllers\Web\School\MasterDataController::class, 'updateSubject'])->name('update');
                Route::delete('/{subject}', [\App\Http\Controllers\Web\School\MasterDataController::class, 'destroySubject'])->name('destroy');
            });

            // ── KKM ──
            Route::prefix('kkm')->name('kkm.')->group(function () {
                Route::post('/', [\App\Http\Controllers\Web\School\MasterDataController::class, 'storeKkm'])->name('store');
                Route::delete('/{subjectKkm}', [\App\Http\Controllers\Web\School\MasterDataController::class, 'destroyKkm'])->name('destroy');
            });

            // ── Kelas (halaman terpisah) ──
            Route::prefix('classrooms')->name('classrooms.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Web\School\ClassroomController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Web\School\ClassroomController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Web\School\ClassroomController::class, 'store'])->name('store');
                Route::get('/{classroom}/edit', [\App\Http\Controllers\Web\School\ClassroomController::class, 'edit'])->name('edit');
                Route::put('/{classroom}', [\App\Http\Controllers\Web\School\ClassroomController::class, 'update'])->name('update');
                Route::delete('/{classroom}', [\App\Http\Controllers\Web\School\ClassroomController::class, 'destroy'])->name('destroy');
            });

            // ── Sekolah (Super Admin) ──
            Route::prefix('schools')->name('schools.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'store'])->name('store');
                Route::put('/{school}', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'update'])->name('update');
                Route::patch('/{school}/toggle-active', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'toggleActive'])->name('toggle-active');
                Route::delete('/{school}', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'destroy'])->name('destroy');
                Route::get('/trash', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'trash'])->name('trash');
                Route::patch('/{id}/restore', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'restore'])->name('restore');
                Route::delete('/{id}/force-delete', [\App\Http\Controllers\Web\SuperAdmin\SchoolController::class, 'forceDelete'])->name('force-delete');
            });

        });

        // ── KESISWAAN ────────────────────────────────────────
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\School\StudentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\School\StudentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\School\StudentController::class, 'store'])->name('store');
            Route::get('/{student}', [\App\Http\Controllers\Web\School\StudentController::class, 'show'])->name('show');
            Route::get('/{student}/edit', [\App\Http\Controllers\Web\School\StudentController::class, 'edit'])->name('edit');
            Route::put('/{student}', [\App\Http\Controllers\Web\School\StudentController::class, 'update'])->name('update');
            Route::delete('/{student}', [\App\Http\Controllers\Web\School\StudentController::class, 'destroy'])->name('destroy');
            Route::post('/{student}/create-account', [\App\Http\Controllers\Web\School\StudentController::class, 'createAccount'])->name('create-account');
            Route::post('/{student}/assign-classroom', [\App\Http\Controllers\Web\School\StudentController::class, 'assignClassroom'])->name('assign-classroom');
            Route::post('/{student}/parents', [\App\Http\Controllers\Web\School\StudentController::class, 'storeParent'])->name('parents.store');
            Route::delete('/parents/{parent}', [\App\Http\Controllers\Web\School\StudentController::class, 'destroyParent'])->name('parents.destroy');
        });

        // ── PPDB ─────────────────────────────────────────────
        Route::prefix('ppdb')->name('ppdb.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\School\PpdbController::class, 'index'])->name('index');
            Route::post('/periods', [\App\Http\Controllers\Web\School\PpdbController::class, 'storePeriod'])->name('periods.store');
            Route::post('/periods/{period}/toggle', [\App\Http\Controllers\Web\School\PpdbController::class, 'togglePeriod'])->name('periods.toggle');
            Route::delete('/periods/{period}', [\App\Http\Controllers\Web\School\PpdbController::class, 'destroyPeriod'])->name('periods.destroy');
            Route::get('/{registration}', [\App\Http\Controllers\Web\School\PpdbController::class, 'show'])->name('show');
            Route::put('/{registration}/status', [\App\Http\Controllers\Web\School\PpdbController::class, 'updateStatus'])->name('update-status');
        });

        // ── PENGUMUMAN ───────────────────────────────────────
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\School\AnnouncementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\School\AnnouncementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\School\AnnouncementController::class, 'store'])->name('store');
            Route::get('/{announcement}/edit', [\App\Http\Controllers\Web\School\AnnouncementController::class, 'edit'])->name('edit');
            Route::put('/{announcement}', [\App\Http\Controllers\Web\School\AnnouncementController::class, 'update'])->name('update');
            Route::post('/{announcement}/toggle-publish', [\App\Http\Controllers\Web\School\AnnouncementController::class, 'togglePublish'])->name('toggle-publish');
            Route::delete('/{announcement}', [\App\Http\Controllers\Web\School\AnnouncementController::class, 'destroy'])->name('destroy');
        });

        // ── RAPOT ────────────────────────────────────────────
        Route::prefix('report-cards')->name('report-cards.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\School\ReportCardController::class, 'index'])->name('index');
            Route::post('/generate', [\App\Http\Controllers\Web\School\ReportCardController::class, 'generate'])->name('generate');
            Route::get('/{reportCard}', [\App\Http\Controllers\Web\School\ReportCardController::class, 'show'])->name('show');
            Route::get('/{reportCard}/pdf', [\App\Http\Controllers\Web\School\ReportCardController::class, 'exportPdf'])->name('pdf');
            Route::post('/{reportCard}/toggle-publish', [\App\Http\Controllers\Web\School\ReportCardController::class, 'togglePublish'])->name('toggle-publish');
            Route::put('/{reportCard}/notes', [\App\Http\Controllers\Web\School\ReportCardController::class, 'updateNotes'])->name('update-notes');
        });

        // ── PENILAIAN ────────────────────────────────────────
        Route::prefix('scores')->name('scores.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\School\ScoreController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Web\School\ScoreController::class, 'store'])->name('store');
            Route::post('/assessment-types', [\App\Http\Controllers\Web\School\ScoreController::class, 'storeAssessmentType'])->name('assessment-types.store');
            Route::delete('/assessment-types/{assessmentType}', [\App\Http\Controllers\Web\School\ScoreController::class, 'destroyAssessmentType'])->name('assessment-types.destroy');
        });

        // ── ABSENSI ──────────────────────────────────────────
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\School\AttendanceController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Web\School\AttendanceController::class, 'store'])->name('store');
            Route::get('/recap', [\App\Http\Controllers\Web\School\AttendanceController::class, 'recap'])->name('recap');
        });

        // ── JADWAL PELAJARAN ─────────────────────────────────
        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\School\ScheduleController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Web\School\ScheduleController::class, 'store'])->name('store');
            Route::delete('/{schedule}', [\App\Http\Controllers\Web\School\ScheduleController::class, 'destroy'])->name('destroy');
        });

        // ── KEPEGAWAIAN ──────────────────────────────────────
        Route::prefix('teachers')->name('teachers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\School\TeacherController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\School\TeacherController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\School\TeacherController::class, 'store'])->name('store');
            Route::get('/{teacher}', [\App\Http\Controllers\Web\School\TeacherController::class, 'show'])->name('show');
            Route::get('/{teacher}/edit', [\App\Http\Controllers\Web\School\TeacherController::class, 'edit'])->name('edit');
            Route::put('/{teacher}', [\App\Http\Controllers\Web\School\TeacherController::class, 'update'])->name('update');
            Route::patch('/{teacher}/toggle-active', [\App\Http\Controllers\Web\School\TeacherController::class, 'toggleActive'])->name('toggle-active');
            Route::delete('/{teacher}', [\App\Http\Controllers\Web\School\TeacherController::class, 'destroy'])->name('destroy');
            Route::post('/{teacher}/create-account', [\App\Http\Controllers\Web\School\TeacherController::class, 'createAccount'])->name('create-account');
            Route::post('/{teacher}/assignments', [\App\Http\Controllers\Web\School\TeacherController::class, 'storeAssignment'])->name('assignments.store');
            Route::delete('/assignments/{assignment}', [\App\Http\Controllers\Web\School\TeacherController::class, 'destroyAssignment'])->name('assignments.destroy');
        });

        // Alias untuk sidebar (classrooms & master)
        Route::get('/classrooms', function () {
            return redirect()->route('master.classrooms.index');
        })->name('classrooms.index');

    });

});
