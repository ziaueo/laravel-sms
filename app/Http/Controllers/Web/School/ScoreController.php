<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Models\Score;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StudentClassroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function index(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;

        $classrooms = Classroom::where('school_id', $school->id)
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->with('gradeLevel')->orderBy('name')->get();

        $classroom = $request->filled('classroom_id')
            ? $classrooms->firstWhere('id', (int) $request->classroom_id)
            : $classrooms->first();

        $subjects = collect();
        $subject  = null;
        $assessmentTypes = AssessmentType::where('school_id', $school->id)->orderBy('order')->orderBy('id')->get();
        $students = collect();
        $scores   = collect();

        if ($classroom) {
            $subjects = Subject::where('school_id', $school->id)
                ->where('is_active', true)
                ->where(function ($q) use ($classroom) {
                    $q->where('grade_level_id', $classroom->grade_level_id)->orWhereNull('grade_level_id');
                })
                ->orderBy('name')->get();

            $subject = $request->filled('subject_id')
                ? $subjects->firstWhere('id', (int) $request->subject_id)
                : $subjects->first();
        }

        if ($classroom && $subject && $activeYear) {
            $studentIds = StudentClassroom::where('classroom_id', $classroom->id)
                ->where('school_year_id', $activeYear->id)
                ->where('is_active', true)->pluck('student_id');

            $students = Student::whereIn('id', $studentIds)->orderBy('full_name')->get();

            // scores[student_id][assessment_type_id] = score
            $scores = Score::where('subject_id', $subject->id)
                ->where('school_year_id', $activeYear->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id')
                ->map(fn($rows) => $rows->keyBy('assessment_type_id'));
        }

        return view('school.scores.index', compact(
            'school', 'activeYear', 'classrooms', 'classroom',
            'subjects', 'subject', 'assessmentTypes', 'students', 'scores'
        ));
    }

    public function store(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;
        if (!$activeYear) return back()->with('error', 'Belum ada tahun ajaran aktif.');

        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id'   => 'required|exists:subjects,id',
            'scores'       => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            foreach (($validated['scores'] ?? []) as $studentId => $byType) {
                foreach ($byType as $assessmentTypeId => $value) {
                    if ($value === null || $value === '') {
                        Score::where([
                            'student_id'         => $studentId,
                            'subject_id'         => $validated['subject_id'],
                            'school_year_id'     => $activeYear->id,
                            'assessment_type_id' => $assessmentTypeId,
                        ])->delete();
                        continue;
                    }
                    Score::updateOrCreate(
                        [
                            'student_id'         => $studentId,
                            'subject_id'         => $validated['subject_id'],
                            'school_year_id'     => $activeYear->id,
                            'assessment_type_id' => $assessmentTypeId,
                        ],
                        [
                            'classroom_id' => $validated['classroom_id'],
                            'score'        => (float) $value,
                            'recorded_by'  => auth()->id(),
                        ]
                    );
                }
            }
            DB::commit();
            return back()->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    // ── Jenis Penilaian ────────────────────────────────
    public function storeAssessmentType(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $request->validate([
            'name'   => 'required|string|max:100',
            'weight' => 'nullable|numeric|min:0|max:100',
            'order'  => 'nullable|integer',
        ]);

        AssessmentType::create([
            'school_id' => $school->id,
            'name'      => $request->name,
            'weight'    => $request->weight ?? 0,
            'order'     => $request->order ?? 0,
        ]);

        return back()->with('success', 'Jenis penilaian berhasil ditambahkan.');
    }

    public function destroyAssessmentType(AssessmentType $assessmentType)
    {
        $school = active_school();
        abort_if(!$school || $assessmentType->school_id !== $school->id, 403);

        $assessmentType->delete();
        return back()->with('success', 'Jenis penilaian berhasil dihapus.');
    }
}
