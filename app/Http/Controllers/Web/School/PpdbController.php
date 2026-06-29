<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Models\PpdbPeriod;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\StudentParent;
use App\Constants\PpdbConstant;
use App\Constants\StudentStatusConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PpdbController extends Controller
{
    public function index(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $periods = PpdbPeriod::where('school_id', $school->id)
            ->with('schoolYear')->withCount('registrations')
            ->orderByDesc('open_date')->get();

        $period = $request->filled('period_id')
            ? $periods->firstWhere('id', (int) $request->period_id)
            : $periods->first();

        $registrations = collect();
        $counts = ['total' => 0, 'pending' => 0, 'diterima' => 0, 'ditolak' => 0];

        if ($period) {
            $query = PpdbRegistration::where('ppdb_period_id', $period->id);

            $all = (clone $query)->get();
            $counts = [
                'total'    => $all->count(),
                'pending'  => $all->whereIn('status', [PpdbConstant::PENDING, PpdbConstant::VERIFIKASI])->count(),
                'diterima' => $all->where('status', PpdbConstant::DITERIMA)->count(),
                'ditolak'  => $all->where('status', PpdbConstant::DITOLAK)->count(),
            ];

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('search')) {
                $query->where('full_name', 'ilike', '%' . $request->search . '%');
            }

            $registrations = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        }

        $activeYear = $school->activeSchoolYear;

        return view('school.ppdb.index', compact('school', 'periods', 'period', 'registrations', 'counts', 'activeYear'));
    }

    public function storePeriod(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $activeYear = $school->activeSchoolYear;
        if (!$activeYear) return back()->with('error', 'Belum ada tahun ajaran aktif.');

        $request->validate([
            'name'        => 'required|string|max:255',
            'open_date'   => 'required|date',
            'close_date'  => 'required|date|after_or_equal:open_date',
            'quota'       => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        PpdbPeriod::create([
            'school_id'      => $school->id,
            'school_year_id' => $activeYear->id,
            'name'           => $request->name,
            'open_date'      => $request->open_date,
            'close_date'     => $request->close_date,
            'quota'          => $request->quota,
            'description'    => $request->description,
            'is_active'      => true,
        ]);

        return back()->with('success', 'Gelombang PPDB berhasil dibuat.');
    }

    public function togglePeriod(PpdbPeriod $period)
    {
        $this->authorizeSchool($period);
        $period->update(['is_active' => !$period->is_active]);
        return back()->with('success', 'Status gelombang diperbarui.');
    }

    public function destroyPeriod(PpdbPeriod $period)
    {
        $this->authorizeSchool($period);
        $period->delete();
        return back()->with('success', 'Gelombang PPDB dihapus.');
    }

    public function show(PpdbRegistration $registration)
    {
        $school = active_school();
        abort_if(!$school || $registration->school_id !== $school->id, 403);

        $registration->load(['ppdbPeriod', 'schoolYear', 'reviewedBy']);
        return view('school.ppdb.show', compact('registration', 'school'));
    }

    public function updateStatus(Request $request, PpdbRegistration $registration)
    {
        $school = active_school();
        abort_if(!$school || $registration->school_id !== $school->id, 403);

        $request->validate([
            'status' => 'required|integer|in:1,2,3,4',
            'notes'  => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $registration->update([
                'status'      => $request->status,
                'notes'       => $request->notes,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Jika diterima → buat data siswa (jika belum)
            if ((int) $request->status === PpdbConstant::DITERIMA) {
                $this->createStudentFromRegistration($registration);
            }

            DB::commit();
            return back()->with('success', 'Status pendaftar diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    protected function createStudentFromRegistration(PpdbRegistration $reg): void
    {
        // Hindari duplikasi
        $exists = Student::where('school_id', $reg->school_id)
            ->where('full_name', $reg->full_name)
            ->where('birth_date', $reg->birth_date)
            ->exists();
        if ($exists) return;

        $student = Student::create([
            'school_id'   => $reg->school_id,
            'full_name'   => $reg->full_name,
            'gender'      => $reg->gender,
            'birth_place' => $reg->birth_place,
            'birth_date'  => $reg->birth_date,
            'religion'    => $reg->religion,
            'address'     => $reg->address,
            'entry_year'  => now()->year,
            'status'      => StudentStatusConstant::AKTIF,
        ]);

        StudentParent::create([
            'student_id' => $student->id,
            'full_name'  => $reg->parent_name,
            'relation'   => $reg->parent_relation,
            'phone'      => $reg->parent_phone,
            'email'      => $reg->parent_email,
            'job'        => $reg->parent_job,
            'address'    => $reg->parent_address,
            'is_primary' => true,
        ]);
    }

    protected function authorizeSchool(PpdbPeriod $period): void
    {
        $school = active_school();
        abort_if(!$school || $period->school_id !== $school->id, 403);
    }
}
