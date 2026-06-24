<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\SchoolYearRequest;
use App\Http\Requests\MasterData\GradeLevelRequest;
use App\Http\Requests\MasterData\SubjectRequest;
use App\Http\Requests\MasterData\SubjectKkmRequest;
use App\Models\SchoolYear;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\SubjectKkm;
use App\Models\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterDataController extends Controller
{
    // ── INDEX ──────────────────────────────────────────
    public function index(Request $request)
    {
        $school     = active_school();
        $activeTab  = $request->get('tab', 'tahun-ajaran');
        $curriculums = Curriculum::where('is_active', true)->get();

        $schoolYears  = SchoolYear::where('school_id', $school->id)
            ->with('curriculum')
            ->orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(10, ['*'], 'page_sy')
            ->withQueryString();

        $gradeLevels = GradeLevel::where('school_id', $school->id)
            ->orderBy('order')
            ->paginate(10, ['*'], 'page_gl')
            ->withQueryString();

        $subjects = Subject::where('school_id', $school->id)
            ->with(['gradeLevel', 'major'])
            ->orderBy('grade_level_id')
            ->paginate(10, ['*'], 'page_sub')
            ->withQueryString();

        $kkms = SubjectKkm::whereHas('subject', fn($q) => $q->where('school_id', $school->id))
            ->with(['subject.gradeLevel', 'schoolYear'])
            ->orderBy('school_year_id', 'desc')
            ->paginate(10, ['*'], 'page_kkm')
            ->withQueryString();

        return view('school.master-data.index', compact(
            'school', 'activeTab', 'curriculums',
            'schoolYears', 'gradeLevels', 'subjects', 'kkms'
        ));
    }

    // ── SCHOOL YEAR ────────────────────────────────────
    public function storeSchoolYear(SchoolYearRequest $request)
    {
        $school = active_school();

        SchoolYear::create([
            'school_id'     => $school->id,
            'curriculum_id' => $request->curriculum_id,
            'name'          => $request->year . ' - Semester ' . $request->semester,
            'year'          => $request->year,
            'semester'      => $request->semester,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'is_active'     => false,
        ]);

        return back()->with('success', 'Tahun ajaran berhasil ditambahkan.')->withFragment('tahun-ajaran');
    }

    public function updateSchoolYear(SchoolYearRequest $request, SchoolYear $schoolYear)
    {
        $schoolYear->update([
            'curriculum_id' => $request->curriculum_id,
            'name'          => $request->year . ' - Semester ' . $request->semester,
            'year'          => $request->year,
            'semester'      => $request->semester,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
        ]);

        return back()->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function setActiveSchoolYear(SchoolYear $schoolYear)
    {
        $school = active_school();

        DB::transaction(function () use ($school, $schoolYear) {
            SchoolYear::where('school_id', $school->id)->update(['is_active' => false]);
            $schoolYear->update(['is_active' => true]);
        });

        return back()->with('success', "Tahun ajaran \"{$schoolYear->name}\" sekarang aktif.");
    }

    public function destroySchoolYear(SchoolYear $schoolYear)
    {
        $schoolYear->delete();
        return back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    // ── GRADE LEVEL ────────────────────────────────────
    public function storeGradeLevel(GradeLevelRequest $request)
    {
        $school = active_school();

        GradeLevel::create([
            'school_id' => $school->id,
            'name'      => $request->name,
            'order'     => $request->order,
        ]);

        return back()->with('success', 'Tingkat kelas berhasil ditambahkan.');
    }

    public function updateGradeLevel(GradeLevelRequest $request, GradeLevel $gradeLevel)
    {
        $gradeLevel->update([
            'name'  => $request->name,
            'order' => $request->order,
        ]);

        return back()->with('success', 'Tingkat kelas berhasil diperbarui.');
    }

    public function destroyGradeLevel(GradeLevel $gradeLevel)
    {
        $gradeLevel->delete();
        return back()->with('success', 'Tingkat kelas berhasil dihapus.');
    }

    // ── SUBJECT ────────────────────────────────────────
    public function storeSubject(SubjectRequest $request)
    {
        $school = active_school();

        Subject::create([
            'school_id'      => $school->id,
            'grade_level_id' => $request->grade_level_id,
            'major_id'       => $request->major_id,
            'name'           => $request->name,
            'code'           => $request->code,
            'is_active'      => true,
        ]);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function updateSubject(SubjectRequest $request, Subject $subject)
    {
        $subject->update([
            'grade_level_id' => $request->grade_level_id,
            'major_id'       => $request->major_id,
            'name'           => $request->name,
            'code'           => $request->code,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroySubject(Subject $subject)
    {
        $subject->delete();
        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    // ── KKM ────────────────────────────────────────────
    public function storeKkm(SubjectKkmRequest $request)
    {
        SubjectKkm::updateOrCreate(
            [
                'subject_id'     => $request->subject_id,
                'school_year_id' => $request->school_year_id,
            ],
            ['kkm_score' => $request->kkm_score]
        );

        return back()->with('success', 'KKM berhasil disimpan.');
    }

    public function destroyKkm(SubjectKkm $subjectKkm)
    {
        $subjectKkm->delete();
        return back()->with('success', 'KKM berhasil dihapus.');
    }
}
