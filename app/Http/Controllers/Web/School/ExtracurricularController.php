<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use App\Models\StudentExtracurricular;
use App\Models\Student;
use App\Models\StudentClassroom;
use Illuminate\Http\Request;

class ExtracurricularController extends Controller
{
    public function index()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $extracurriculars = Extracurricular::where('school_id', $school->id)
            ->orderBy('name')->get();

        return view('school.extracurriculars.index', compact('school', 'extracurriculars'));
    }

    public function store(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Extracurricular::create([
            'school_id'   => $school->id,
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Ekstrakurikuler ditambahkan.');
    }

    public function update(Request $request, Extracurricular $extracurricular)
    {
        $this->authorizeSchool($extracurricular);
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $extracurricular->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);
        return back()->with('success', 'Ekstrakurikuler diperbarui.');
    }

    public function destroy(Extracurricular $extracurricular)
    {
        $this->authorizeSchool($extracurricular);
        $extracurricular->delete();
        return back()->with('success', 'Ekstrakurikuler dihapus.');
    }

    public function show(Extracurricular $extracurricular)
    {
        $this->authorizeSchool($extracurricular);
        $school = active_school();
        $activeYear = $school->activeSchoolYear;

        $members = StudentExtracurricular::where('extracurricular_id', $extracurricular->id)
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->with('student')->get();

        $memberIds = $members->pluck('student_id')->all();

        $students = collect();
        if ($activeYear) {
            $studentIds = StudentClassroom::where('school_year_id', $activeYear->id)
                ->where('is_active', true)
                ->whereHas('classroom', fn($q) => $q->where('school_id', $school->id))
                ->pluck('student_id');
            $students = Student::whereIn('id', $studentIds)
                ->whereNotIn('id', $memberIds)
                ->orderBy('full_name')->get();
        }

        return view('school.extracurriculars.show', compact('school', 'extracurricular', 'members', 'students', 'activeYear'));
    }

    public function addMember(Request $request, Extracurricular $extracurricular)
    {
        $this->authorizeSchool($extracurricular);
        $school = active_school();
        $activeYear = $school->activeSchoolYear;
        if (!$activeYear) return back()->with('error', 'Belum ada tahun ajaran aktif.');

        $request->validate(['student_id' => 'required|exists:students,id']);

        StudentExtracurricular::firstOrCreate([
            'student_id'         => $request->student_id,
            'extracurricular_id' => $extracurricular->id,
            'school_year_id'     => $activeYear->id,
        ]);

        return back()->with('success', 'Anggota ditambahkan.');
    }

    public function removeMember(StudentExtracurricular $member)
    {
        $member->delete();
        return back()->with('success', 'Anggota dihapus.');
    }

    protected function authorizeSchool(Extracurricular $ekskul): void
    {
        $school = active_school();
        abort_if(!$school || $ekskul->school_id !== $school->id, 403);
    }
}
