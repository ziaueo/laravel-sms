<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Constants\ScheduleConstant;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;

        $classrooms = Classroom::where('school_id', $school->id)
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->with('gradeLevel')
            ->orderBy('name')
            ->get();

        $classroom = null;
        $schedulesByDay = collect();
        $subjects = collect();
        $teachers = collect();

        if ($request->filled('classroom_id')) {
            $classroom = $classrooms->firstWhere('id', (int) $request->classroom_id);
        } elseif ($classrooms->count()) {
            $classroom = $classrooms->first();
        }

        if ($classroom && $activeYear) {
            $schedulesByDay = Schedule::where('classroom_id', $classroom->id)
                ->where('school_year_id', $activeYear->id)
                ->with(['subject', 'teacher'])
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');

            $subjects = Subject::where('school_id', $school->id)
                ->where('is_active', true)
                ->where(function ($q) use ($classroom) {
                    $q->where('grade_level_id', $classroom->grade_level_id)
                      ->orWhereNull('grade_level_id');
                })
                ->orderBy('name')
                ->get();

            $teachers = Teacher::where('school_id', $school->id)
                ->where('is_active', true)
                ->orderBy('full_name')
                ->get();
        }

        return view('school.schedules.index', compact(
            'school', 'activeYear', 'classrooms', 'classroom', 'schedulesByDay', 'subjects', 'teachers'
        ));
    }

    public function store(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;
        if (!$activeYear) {
            return back()->with('error', 'Belum ada tahun ajaran aktif.');
        }

        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'day_of_week'  => 'required|integer|between:1,6',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'type'         => 'required|integer|in:1,2,3,4',
            'subject_id'   => 'nullable|exists:subjects,id',
            'teacher_id'   => 'nullable|exists:teachers,id',
        ]);

        // Pelajaran wajib ada mapel
        if ($validated['type'] == ScheduleConstant::PELAJARAN && empty($validated['subject_id'])) {
            return back()->with('error', 'Jadwal pelajaran harus memilih mata pelajaran.');
        }

        // Cek bentrok jam di kelas yang sama
        $clash = Schedule::where('classroom_id', $validated['classroom_id'])
            ->where('school_year_id', $activeYear->id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($clash) {
            return back()->with('error', 'Jadwal bentrok dengan slot waktu yang sudah ada di hari tersebut.');
        }

        Schedule::create([
            'classroom_id'   => $validated['classroom_id'],
            'school_year_id' => $activeYear->id,
            'day_of_week'    => $validated['day_of_week'],
            'start_time'     => $validated['start_time'],
            'end_time'       => $validated['end_time'],
            'type'           => $validated['type'],
            'subject_id'     => $validated['type'] == ScheduleConstant::PELAJARAN ? $validated['subject_id'] : null,
            'teacher_id'     => $validated['type'] == ScheduleConstant::PELAJARAN ? $validated['teacher_id'] : null,
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy(Schedule $schedule)
    {
        $school = active_school();
        abort_if(!$school || $schedule->classroom->school_id !== $school->id, 403);

        $classroomId = $schedule->classroom_id;
        $schedule->delete();

        return redirect()->route('schedules.index', ['classroom_id' => $classroomId])
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
