<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentClassroom;
use App\Constants\AttendanceConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
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

        $date = $request->filled('date') ? $request->date : now()->toDateString();

        $students = collect();
        $existing = collect();

        if ($classroom && $activeYear) {
            $studentIds = StudentClassroom::where('classroom_id', $classroom->id)
                ->where('school_year_id', $activeYear->id)
                ->where('is_active', true)
                ->pluck('student_id');

            $students = Student::whereIn('id', $studentIds)
                ->orderBy('full_name')->get();

            $existing = Attendance::where('classroom_id', $classroom->id)
                ->where('date', $date)
                ->get()->keyBy('student_id');
        }

        return view('school.attendances.index', compact(
            'school', 'activeYear', 'classrooms', 'classroom', 'date', 'students', 'existing'
        ));
    }

    public function store(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;
        if (!$activeYear) return back()->with('error', 'Belum ada tahun ajaran aktif.');

        $validated = $request->validate([
            'classroom_id'        => 'required|exists:classrooms,id',
            'date'                => 'required|date',
            'status'              => 'required|array',
            'status.*'            => 'integer|in:1,2,3,4,5',
            'notes'               => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['status'] as $studentId => $status) {
                Attendance::updateOrCreate(
                    ['student_id' => $studentId, 'date' => $validated['date']],
                    [
                        'classroom_id'   => $validated['classroom_id'],
                        'school_year_id' => $activeYear->id,
                        'status'         => $status,
                        'source'         => AttendanceConstant::SOURCE_MANUAL,
                        'notes'          => $request->input("notes.$studentId"),
                        'recorded_by'    => auth()->id(),
                    ]
                );
            }
            DB::commit();
            return back()->with('success', 'Absensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    public function recap(Request $request)
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

        $month = $request->filled('month') ? $request->month : now()->format('Y-m');
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $students = collect();
        $recap    = collect();

        if ($classroom && $activeYear) {
            $studentIds = StudentClassroom::where('classroom_id', $classroom->id)
                ->where('school_year_id', $activeYear->id)
                ->where('is_active', true)
                ->pluck('student_id');

            $students = Student::whereIn('id', $studentIds)->orderBy('full_name')->get();

            $records = Attendance::where('classroom_id', $classroom->id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->get();

            // recap[student_id] = ['H'=>x,'S'=>x,'I'=>x,'A'=>x]
            $recap = $students->mapWithKeys(function ($s) use ($records) {
                $r = $records->where('student_id', $s->id);
                return [$s->id => [
                    'hadir' => $r->where('status', AttendanceConstant::HADIR)->count(),
                    'sakit' => $r->where('status', AttendanceConstant::SAKIT)->count(),
                    'izin'  => $r->where('status', AttendanceConstant::IZIN)->count(),
                    'alpa'  => $r->where('status', AttendanceConstant::ALPA)->count(),
                ]];
            });
        }

        return view('school.attendances.recap', compact(
            'school', 'activeYear', 'classrooms', 'classroom', 'month', 'students', 'recap'
        ));
    }
}
