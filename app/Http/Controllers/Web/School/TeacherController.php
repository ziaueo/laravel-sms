<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\Position;
use App\Models\User;
use App\Models\UserSchool;
use App\Models\TeachingAssignment;
use App\Models\SchoolYear;
use App\Models\Classroom;
use App\Models\Subject;
use App\Helpers\FileHelper;
use App\Constants\RoleConstant;
use App\Constants\GenderConstant;
use App\Constants\EmploymentConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $query = Teacher::where('school_id', $school->id)
            ->with(['position', 'user'])
            ->withCount('teachingAssignments');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('nip', 'ilike', '%' . $request->search . '%');
            });
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $teachers  = $query->orderBy('full_name')->paginate(12)->withQueryString();
        $positions = Position::orderBy('name')->get();

        return view('school.teachers.index', compact('teachers', 'positions', 'school'));
    }

    public function create()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $positions = Position::orderBy('name')->get();

        return view('school.teachers.create', compact('school', 'positions'));
    }

    public function store(StoreTeacherRequest $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        DB::beginTransaction();
        try {
            $teacher = Teacher::create([
                'school_id'         => $school->id,
                'full_name'         => $request->full_name,
                'gender'            => $request->gender,
                'nip'               => $request->nip,
                'birth_place'       => $request->birth_place,
                'birth_date'        => $request->birth_date,
                'religion'          => $request->religion,
                'address'           => $request->address,
                'phone'             => $request->phone,
                'email'             => $request->email,
                'join_date'         => $request->join_date,
                'employment_status' => $request->employment_status,
                'position_id'       => $request->position_id,
                'last_education'    => $request->last_education,
                'major'             => $request->major,
                'is_active'         => true,
            ]);

            if ($request->hasFile('photo')) {
                $path = FileHelper::upload($request->file('photo'), 'teacher_photo', $teacher->id);
                $teacher->update(['photo' => $path]);
            }

            DB::commit();

            return redirect()->route('teachers.show', hashid_encode($teacher->id, Teacher::class))
                ->with('success', "Data guru \"{$teacher->full_name}\" berhasil ditambahkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan guru: ' . $e->getMessage());
        }
    }

    public function show(string $teacher)
    {
        $teacher = $this->findTeacher($teacher);
        $teacher->load(['position', 'user', 'school']);

        $teachingAssignments = TeachingAssignment::where('teacher_id', $teacher->id)
            ->with(['subject', 'classroom.gradeLevel', 'schoolYear'])
            ->orderBy('school_year_id', 'desc')
            ->get()
            ->groupBy('schoolYear.name');

        $activeSchoolYear = SchoolYear::where('school_id', $teacher->school_id)
            ->where('is_active', true)
            ->first();

        $classrooms = Classroom::where('school_id', $teacher->school_id)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with('gradeLevel')
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('school_id', $teacher->school_id)
            ->where('is_active', true)
            ->with('gradeLevel')
            ->orderBy('name')
            ->get();

        return view('school.teachers.show', compact(
            'teacher', 'teachingAssignments',
            'activeSchoolYear', 'classrooms', 'subjects'
        ));
    }

    public function edit(string $teacher)
    {
        $teacher   = $this->findTeacher($teacher);
        $school    = active_school();
        $positions = Position::orderBy('name')->get();

        return view('school.teachers.edit', compact('teacher', 'positions', 'school'));
    }

    public function update(UpdateTeacherRequest $request, string $teacher)
    {
        $teacher = $this->findTeacher($teacher);

        DB::beginTransaction();
        try {
            $teacher->update([
                'full_name'         => $request->full_name,
                'gender'            => $request->gender,
                'nip'               => $request->nip,
                'birth_place'       => $request->birth_place,
                'birth_date'        => $request->birth_date,
                'religion'          => $request->religion,
                'address'           => $request->address,
                'phone'             => $request->phone,
                'email'             => $request->email,
                'join_date'         => $request->join_date,
                'employment_status' => $request->employment_status,
                'position_id'       => $request->position_id,
                'last_education'    => $request->last_education,
                'major'             => $request->major,
                'is_active'         => $request->boolean('is_active', true),
            ]);

            if ($request->hasFile('photo')) {
                FileHelper::delete($teacher->photo);
                $path = FileHelper::upload($request->file('photo'), 'teacher_photo', $teacher->id);
                $teacher->update(['photo' => $path]);
            }

            DB::commit();

            return redirect()->route('teachers.show', hashid_encode($teacher->id, Teacher::class))
                ->with('success', "Data guru \"{$teacher->full_name}\" berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui guru: ' . $e->getMessage());
        }
    }

    public function toggleActive(string $teacher)
    {
        $teacher = $this->findTeacher($teacher);
        $teacher->update(['is_active' => !$teacher->is_active]);
        $status = $teacher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Guru \"{$teacher->full_name}\" berhasil {$status}.");
    }

    public function destroy(string $teacher)
    {
        $teacher = $this->findTeacher($teacher);
        $name = $teacher->full_name;
        FileHelper::delete($teacher->photo);
        $teacher->delete();
        return redirect()->route('teachers.index')
            ->with('success', "Data guru \"{$name}\" berhasil dihapus.");
    }

    // ── Buat Akun Login ────────────────────────────────
    public function createAccount(string $teacher)
    {
        $teacher = $this->findTeacher($teacher);

        if ($teacher->user_id) {
            return back()->with('error', 'Guru ini sudah memiliki akun login.');
        }

        if (!$teacher->email) {
            return back()->with('error', 'Email guru harus diisi terlebih dahulu sebelum membuat akun.');
        }

        if (User::where('email', $teacher->email)->exists()) {
            return back()->with('error', 'Email ini sudah digunakan oleh akun lain.');
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'                 => $teacher->full_name,
                'email'                => $teacher->email,
                'password'             => Hash::make('P@ssw0rd'),
                'is_active'            => true,
                'must_change_password' => true,
                'email_verified_at'    => now(),
            ]);

            $user->assignRole(RoleConstant::getSpatieRole(RoleConstant::GURU));

            UserSchool::create([
                'user_id'   => $user->id,
                'school_id' => $teacher->school_id,
                'role'      => RoleConstant::GURU,
            ]);

            $teacher->update(['user_id' => $user->id]);

            DB::commit();

            return back()->with('success', "Akun login untuk \"{$teacher->full_name}\" berhasil dibuat. Password default: P@ssw0rd");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat akun: ' . $e->getMessage());
        }
    }

    // ── Teaching Assignment ─────────────────────────────
    public function storeAssignment(Request $request, string $teacher)
    {
        $teacher = $this->findTeacher($teacher);

        $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'classroom_id'   => 'required|exists:classrooms,id',
            'subject_id'     => 'required|exists:subjects,id',
        ]);

        $exists = TeachingAssignment::where([
            'teacher_id'     => $teacher->id,
            'classroom_id'   => $request->classroom_id,
            'subject_id'     => $request->subject_id,
            'school_year_id' => $request->school_year_id,
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Penugasan ini sudah ada.');
        }

        TeachingAssignment::create([
            'teacher_id'     => $teacher->id,
            'classroom_id'   => $request->classroom_id,
            'subject_id'     => $request->subject_id,
            'school_year_id' => $request->school_year_id,
            'is_active'      => true,
        ]);

        return back()->with('success', 'Penugasan mengajar berhasil ditambahkan.');
    }

    public function destroyAssignment(string $assignment)
    {
        $assignment = $this->findAssignment($assignment);
        $assignment->delete();
        return back()->with('success', 'Penugasan mengajar berhasil dihapus.');
    }

    // ── Helper: decode hashid → Teacher milik sekolah aktif ─────
    protected function findTeacher(string $hash): Teacher
    {
        $teacher = Teacher::findOrFail(hashid_decode_or_404($hash, Teacher::class));
        $school = active_school();
        abort_if(!$school || $teacher->school_id !== $school->id, 403, 'Guru ini bukan bagian dari sekolah aktif.');
        return $teacher;
    }

    // ── Helper: decode hashid → TeachingAssignment milik sekolah aktif ─────
    protected function findAssignment(string $hash): TeachingAssignment
    {
        $assignment = TeachingAssignment::with('teacher')
            ->findOrFail(hashid_decode_or_404($hash, TeachingAssignment::class));
        $school = active_school();
        abort_if(!$school || !$assignment->teacher || $assignment->teacher->school_id !== $school->id, 403);
        return $assignment;
    }
}
