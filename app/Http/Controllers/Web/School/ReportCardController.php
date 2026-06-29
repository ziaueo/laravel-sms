<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Models\FinalScore;
use App\Models\Score;
use App\Models\AssessmentType;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StudentClassroom;
use App\Constants\AttendanceConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardController extends Controller
{
    public function index(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;

        $classrooms = Classroom::where('school_id', $school->id)
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->orderBy('name')->get();

        $classroom = $request->filled('classroom_id')
            ? $classrooms->firstWhere('id', (int) $request->classroom_id)
            : $classrooms->first();

        $students = collect();
        $reportCards = collect();

        if ($classroom && $activeYear) {
            $studentIds = StudentClassroom::where('classroom_id', $classroom->id)
                ->where('school_year_id', $activeYear->id)
                ->where('is_active', true)->pluck('student_id');

            $students = Student::whereIn('id', $studentIds)->orderBy('full_name')->get();

            $reportCards = ReportCard::where('classroom_id', $classroom->id)
                ->where('school_year_id', $activeYear->id)
                ->get()->keyBy('student_id');
        }

        return view('school.report-cards.index', compact(
            'school', 'activeYear', 'classrooms', 'classroom', 'students', 'reportCards'
        ));
    }

    public function generate(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;
        if (!$activeYear) return back()->with('error', 'Belum ada tahun ajaran aktif.');

        $request->validate(['classroom_id' => 'required|exists:classrooms,id']);
        $classroom = Classroom::findOrFail($request->classroom_id);

        $studentIds = StudentClassroom::where('classroom_id', $classroom->id)
            ->where('school_year_id', $activeYear->id)
            ->where('is_active', true)->pluck('student_id');

        $students = Student::whereIn('id', $studentIds)->get();

        $subjects = Subject::where('school_id', $school->id)
            ->where('is_active', true)
            ->where(function ($q) use ($classroom) {
                $q->where('grade_level_id', $classroom->grade_level_id)->orWhereNull('grade_level_id');
            })->get();

        $assessmentTypes = AssessmentType::where('school_id', $school->id)->get()->keyBy('id');
        $totalWeight = $assessmentTypes->sum('weight');

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                $finals = [];

                foreach ($subjects as $subject) {
                    $scores = Score::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->where('school_year_id', $activeYear->id)
                        ->whereNotNull('score')->get();

                    if ($scores->isEmpty()) continue;

                    if ($totalWeight > 0) {
                        $weightedSum = 0; $weightUsed = 0;
                        foreach ($scores as $sc) {
                            $w = $assessmentTypes->get($sc->assessment_type_id)?->weight ?? 0;
                            if ($w > 0) { $weightedSum += $sc->score * $w; $weightUsed += $w; }
                        }
                        $final = $weightUsed > 0 ? round($weightedSum / $weightUsed, 2) : round($scores->avg('score'), 2);
                    } else {
                        $final = round($scores->avg('score'), 2);
                    }

                    FinalScore::updateOrCreate(
                        [
                            'student_id'     => $student->id,
                            'subject_id'     => $subject->id,
                            'school_year_id' => $activeYear->id,
                        ],
                        [
                            'classroom_id' => $classroom->id,
                            'final_score'  => $final,
                            'grade'        => $this->letterGrade($final),
                            'predicate'    => $this->predicate($final),
                        ]
                    );
                    $finals[] = $final;
                }

                $gpa = count($finals) ? round(array_sum($finals) / count($finals), 2) : null;

                // Absensi
                $att = Attendance::where('student_id', $student->id)
                    ->where('school_year_id', $activeYear->id)->get();

                ReportCard::updateOrCreate(
                    ['student_id' => $student->id, 'school_year_id' => $activeYear->id],
                    [
                        'classroom_id' => $classroom->id,
                        'gpa'          => $gpa,
                        'total_hadir'  => $att->where('status', AttendanceConstant::HADIR)->count(),
                        'total_sakit'  => $att->where('status', AttendanceConstant::SAKIT)->count(),
                        'total_izin'   => $att->where('status', AttendanceConstant::IZIN)->count(),
                        'total_alpa'   => $att->where('status', AttendanceConstant::ALPA)->count(),
                    ]
                );
            }

            // Ranking berdasarkan gpa
            $this->recalculateRanks($classroom->id, $activeYear->id);

            DB::commit();
            return back()->with('success', 'Rapot berhasil di-generate untuk ' . $students->count() . ' siswa.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate rapot: ' . $e->getMessage());
        }
    }

    public function show(ReportCard $reportCard)
    {
        $this->authorizeSchool($reportCard);
        [$student, $school, $year, $finalScores] = $this->buildData($reportCard);

        return view('school.report-cards.show', compact('reportCard', 'student', 'school', 'year', 'finalScores'));
    }

    public function exportPdf(ReportCard $reportCard)
    {
        $this->authorizeSchool($reportCard);
        [$student, $school, $year, $finalScores] = $this->buildData($reportCard);

        $pdf = Pdf::loadView('school.report-cards.pdf', compact('reportCard', 'student', 'school', 'year', 'finalScores'))
            ->setPaper('a4', 'portrait');

        $filename = 'Rapot_' . str_replace(' ', '_', $student->full_name) . '.pdf';
        return $pdf->download($filename);
    }

    public function togglePublish(ReportCard $reportCard)
    {
        $this->authorizeSchool($reportCard);

        $reportCard->update([
            'is_published' => !$reportCard->is_published,
            'published_at' => !$reportCard->is_published ? now() : null,
        ]);

        $status = $reportCard->is_published ? 'dipublikasikan' : 'ditarik dari publikasi';
        return back()->with('success', "Rapot {$status}.");
    }

    public function updateNotes(Request $request, ReportCard $reportCard)
    {
        $this->authorizeSchool($reportCard);
        $request->validate([
            'homeroom_notes'  => 'nullable|string',
            'principal_notes' => 'nullable|string',
        ]);
        $reportCard->update($request->only('homeroom_notes', 'principal_notes'));
        return back()->with('success', 'Catatan rapot disimpan.');
    }

    // ── Helpers ────────────────────────────────────────
    protected function buildData(ReportCard $reportCard): array
    {
        $student = $reportCard->student;
        $year    = $reportCard->schoolYear;
        $school  = $reportCard->classroom->school;

        $finalScores = FinalScore::where('student_id', $student->id)
            ->where('school_year_id', $year->id)
            ->with('subject')
            ->get()
            ->sortBy('subject.name')
            ->values();

        return [$student, $school, $year, $finalScores];
    }

    protected function recalculateRanks(int $classroomId, int $yearId): void
    {
        $cards = ReportCard::where('classroom_id', $classroomId)
            ->where('school_year_id', $yearId)
            ->whereNotNull('gpa')
            ->orderByDesc('gpa')->get();

        $rank = 1;
        foreach ($cards as $card) {
            $card->update(['rank_in_class' => $rank++]);
        }
    }

    protected function letterGrade(float $score): string
    {
        return match(true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default      => 'E',
        };
    }

    protected function predicate(float $score): string
    {
        return match(true) {
            $score >= 90 => 'Sangat Baik',
            $score >= 80 => 'Baik',
            $score >= 70 => 'Cukup',
            default      => 'Perlu Bimbingan',
        };
    }

    protected function authorizeSchool(ReportCard $reportCard): void
    {
        $school = active_school();
        abort_if(!$school || $reportCard->classroom->school_id !== $school->id, 403);
    }
}
