<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\ClassroomRequest;
use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\GradeLevel;
use App\Models\Teacher;
use App\Models\Major;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $school = active_school();

        $activeYearId = $request->get(
            'school_year_id',
            SchoolYear::where('school_id', $school->id)->where('is_active', true)->value('id')
        );

        $schoolYears = SchoolYear::where('school_id', $school->id)
            ->orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $gradeLevels = GradeLevel::where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        $classrooms = Classroom::where('school_id', $school->id)
            ->when($activeYearId, fn($q) => $q->where('school_year_id', $activeYearId))
            ->with(['gradeLevel', 'homeroomTeacher', 'major'])
            ->withCount('students')
            ->orderBy('grade_level_id')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $teachers = Teacher::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        $majors = Major::where('school_id', $school->id)
            ->where('is_active', true)
            ->get();

        return view('school.classrooms.index', compact(
            'school', 'schoolYears', 'gradeLevels',
            'classrooms', 'teachers', 'majors', 'activeYearId'
        ));
    }

    public function create()
    {
        $school = active_school();

        $schoolYears = SchoolYear::where('school_id', $school->id)
            ->orderBy('year', 'desc')
            ->get();

        $gradeLevels = GradeLevel::where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        $teachers = Teacher::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        $majors = Major::where('school_id', $school->id)
            ->where('is_active', true)
            ->get();

        $activeSchoolYear = SchoolYear::where('school_id', $school->id)
            ->where('is_active', true)
            ->first();

        return view('school.classrooms.create', compact(
            'school', 'schoolYears', 'gradeLevels',
            'teachers', 'majors', 'activeSchoolYear'
        ));
    }

    public function store(ClassroomRequest $request)
    {
        $school = active_school();

        Classroom::create([
            'school_id'           => $school->id,
            'school_year_id'      => $request->school_year_id,
            'grade_level_id'      => $request->grade_level_id,
            'homeroom_teacher_id' => $request->homeroom_teacher_id,
            'major_id'            => $request->major_id,
            'name'                => $request->name,
            'capacity'            => $request->capacity,
            'is_active'           => true,
        ]);

        return redirect()->route('classrooms.index')
            ->with('success', "Kelas \"{$request->name}\" berhasil ditambahkan.");
    }

    public function edit(Classroom $classroom)
    {
        $school = active_school();

        $schoolYears = SchoolYear::where('school_id', $school->id)
            ->orderBy('year', 'desc')
            ->get();

        $gradeLevels = GradeLevel::where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        $teachers = Teacher::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        $majors = Major::where('school_id', $school->id)
            ->where('is_active', true)
            ->get();

        return view('school.classrooms.edit', compact(
            'classroom', 'schoolYears', 'gradeLevels', 'teachers', 'majors'
        ));
    }

    public function update(ClassroomRequest $request, Classroom $classroom)
    {
        $classroom->update([
            'school_year_id'      => $request->school_year_id,
            'grade_level_id'      => $request->grade_level_id,
            'homeroom_teacher_id' => $request->homeroom_teacher_id,
            'major_id'            => $request->major_id,
            'name'                => $request->name,
            'capacity'            => $request->capacity,
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return redirect()->route('classrooms.index')
            ->with('success', "Kelas \"{$classroom->name}\" berhasil diperbarui.");
    }

    public function destroy(Classroom $classroom)
    {
        $name = $classroom->name;
        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', "Kelas \"{$name}\" berhasil dihapus.");
    }
}
