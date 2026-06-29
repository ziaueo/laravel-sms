<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentClassroom;
use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\User;
use App\Models\UserSchool;
use App\Helpers\FileHelper;
use App\Constants\RoleConstant;
use App\Constants\StudentStatusConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;

        $query = Student::where('school_id', $school->id)
            ->with(['user', 'activeClassroom.classroom.gradeLevel']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('nisn', 'ilike', '%' . $request->search . '%')
                  ->orWhere('nis', 'ilike', '%' . $request->search . '%');
            });
        }

        if ($request->filled('classroom_id')) {
            $query->whereHas('classrooms', function ($q) use ($request) {
                $q->where('classrooms.id', $request->classroom_id)
                  ->where('student_classrooms.is_active', true);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', StudentStatusConstant::AKTIF);
        }

        $students = $query->orderBy('full_name')->paginate(15)->withQueryString();

        $classrooms = Classroom::where('school_id', $school->id)
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->with('gradeLevel')
            ->orderBy('name')
            ->get();

        return view('school.students.index', compact('students', 'classrooms', 'school', 'activeYear'));
    }

    public function create()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;

        $classrooms = Classroom::where('school_id', $school->id)
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->with('gradeLevel')
            ->orderBy('name')
            ->get();

        return view('school.students.create', compact('school', 'classrooms', 'activeYear'));
    }

    public function store(StoreStudentRequest $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        DB::beginTransaction();
        try {
            $student = Student::create([
                'school_id'      => $school->id,
                'full_name'      => $request->full_name,
                'gender'         => $request->gender,
                'nisn'           => $request->nisn,
                'nis'            => $request->nis,
                'birth_place'    => $request->birth_place,
                'birth_date'     => $request->birth_date,
                'religion'       => $request->religion,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'blood_type'     => $request->blood_type,
                'entry_year'     => $request->entry_year,
                'entry_class_id' => $request->entry_class_id,
                'status'         => $request->status ?? StudentStatusConstant::AKTIF,
            ]);

            if ($request->hasFile('photo')) {
                $path = FileHelper::upload($request->file('photo'), 'student_photo', $student->id);
                $student->update(['photo' => $path]);
            }

            // Penempatan kelas otomatis di tahun ajaran aktif
            $activeYear = $school->activeSchoolYear;
            if ($request->entry_class_id && $activeYear) {
                StudentClassroom::create([
                    'student_id'     => $student->id,
                    'classroom_id'   => $request->entry_class_id,
                    'school_year_id' => $activeYear->id,
                    'is_active'      => true,
                ]);
            }

            DB::commit();

            return redirect()->route('students.show', $student->id)
                ->with('success', "Data siswa \"{$student->full_name}\" berhasil ditambahkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage());
        }
    }

    public function show(Student $student)
    {
        $this->authorizeSchool($student);

        $student->load(['user', 'school', 'parents', 'activeClassroom.classroom.gradeLevel']);

        $school     = active_school();
        $activeYear = $school->activeSchoolYear;

        $classrooms = Classroom::where('school_id', $student->school_id)
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->with('gradeLevel')
            ->orderBy('name')
            ->get();

        $classroomHistory = StudentClassroom::where('student_id', $student->id)
            ->with(['classroom.gradeLevel', 'schoolYear'])
            ->orderBy('school_year_id', 'desc')
            ->get();

        return view('school.students.show', compact(
            'student', 'classrooms', 'activeYear', 'classroomHistory'
        ));
    }

    public function edit(Student $student)
    {
        $this->authorizeSchool($student);

        $school     = active_school();
        $activeYear = $school->activeSchoolYear;

        $classrooms = Classroom::where('school_id', $student->school_id)
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->with('gradeLevel')
            ->orderBy('name')
            ->get();

        return view('school.students.edit', compact('student', 'classrooms', 'school', 'activeYear'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $this->authorizeSchool($student);

        DB::beginTransaction();
        try {
            $student->update([
                'full_name'      => $request->full_name,
                'gender'         => $request->gender,
                'nisn'           => $request->nisn,
                'nis'            => $request->nis,
                'birth_place'    => $request->birth_place,
                'birth_date'     => $request->birth_date,
                'religion'       => $request->religion,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'blood_type'     => $request->blood_type,
                'entry_year'     => $request->entry_year,
                'entry_class_id' => $request->entry_class_id,
                'status'         => $request->status,
                'exit_date'      => $request->exit_date,
                'exit_reason'    => $request->exit_reason,
            ]);

            if ($request->hasFile('photo')) {
                FileHelper::delete($student->photo);
                $path = FileHelper::upload($request->file('photo'), 'student_photo', $student->id);
                $student->update(['photo' => $path]);
            }

            DB::commit();

            return redirect()->route('students.show', $student->id)
                ->with('success', "Data siswa \"{$student->full_name}\" berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui siswa: ' . $e->getMessage());
        }
    }

    public function destroy(Student $student)
    {
        $this->authorizeSchool($student);

        $name = $student->full_name;
        FileHelper::delete($student->photo);
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', "Data siswa \"{$name}\" berhasil dihapus.");
    }

    // ── Buat Akun Login Siswa ──────────────────────────
    public function createAccount(Student $student)
    {
        $this->authorizeSchool($student);

        if ($student->user_id) {
            return back()->with('error', 'Siswa ini sudah memiliki akun login.');
        }

        if (!$student->nis && !$student->nisn) {
            return back()->with('error', 'NIS atau NISN harus diisi terlebih dahulu (dipakai sebagai username/email).');
        }

        $email = ($student->nis ?? $student->nisn) . '@siswa.' . $student->school->slug . '.sch.id';

        if (User::where('email', $email)->exists()) {
            return back()->with('error', 'Akun dengan identitas ini sudah ada.');
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'                 => $student->full_name,
                'email'                => $email,
                'password'             => Hash::make('P@ssw0rd'),
                'is_active'            => true,
                'must_change_password' => true,
                'email_verified_at'    => now(),
            ]);

            $user->assignRole(RoleConstant::getSpatieRole(RoleConstant::SISWA));

            UserSchool::create([
                'user_id'   => $user->id,
                'school_id' => $student->school_id,
                'role'      => RoleConstant::SISWA,
            ]);

            $student->update(['user_id' => $user->id]);

            DB::commit();

            return back()->with('success', "Akun login berhasil dibuat. Username: {$email} — Password default: P@ssw0rd");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat akun: ' . $e->getMessage());
        }
    }

    // ── Penempatan Kelas ───────────────────────────────
    public function assignClassroom(Request $request, Student $student)
    {
        $this->authorizeSchool($student);

        $request->validate([
            'classroom_id'   => 'required|exists:classrooms,id',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        // Nonaktifkan penempatan lama di tahun ajaran yang sama, lalu set baru
        StudentClassroom::updateOrCreate(
            [
                'student_id'     => $student->id,
                'school_year_id' => $request->school_year_id,
            ],
            [
                'classroom_id'   => $request->classroom_id,
                'is_active'      => true,
            ]
        );

        // Pastikan hanya 1 penempatan aktif
        StudentClassroom::where('student_id', $student->id)
            ->where('school_year_id', '!=', $request->school_year_id)
            ->update(['is_active' => false]);

        return back()->with('success', 'Penempatan kelas berhasil diperbarui.');
    }

    // ── Manajemen Orang Tua / Wali ─────────────────────
    public function storeParent(Request $request, Student $student)
    {
        $this->authorizeSchool($student);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'relation'  => 'required|integer|in:1,2,3',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'job'       => 'nullable|string|max:100',
            'education' => 'nullable|string|max:50',
            'address'   => 'nullable|string',
        ]);

        $isFirst = $student->parents()->count() === 0;

        StudentParent::create([
            'student_id' => $student->id,
            'full_name'  => $request->full_name,
            'relation'   => $request->relation,
            'gender'     => $request->relation == 1 ? 1 : ($request->relation == 2 ? 2 : null),
            'phone'      => $request->phone,
            'email'      => $request->email,
            'job'        => $request->job,
            'education'  => $request->education,
            'address'    => $request->address,
            'is_primary' => $request->boolean('is_primary') || $isFirst,
        ]);

        return back()->with('success', 'Data orang tua/wali berhasil ditambahkan.');
    }

    public function destroyParent(StudentParent $parent)
    {
        $parent->delete();
        return back()->with('success', 'Data orang tua/wali berhasil dihapus.');
    }

    // ── Helper: pastikan siswa milik sekolah aktif ─────
    protected function authorizeSchool(Student $student): void
    {
        $school = active_school();
        abort_if(!$school || $student->school_id !== $school->id, 403, 'Siswa ini bukan bagian dari sekolah aktif.');
    }
}
