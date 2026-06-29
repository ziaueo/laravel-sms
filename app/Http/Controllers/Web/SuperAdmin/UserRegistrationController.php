<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\UserRegistration;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentParent;
use App\Constants\RoleConstant;
use App\Constants\RegistrationConstant;
use App\Constants\ParentRelationConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = UserRegistration::with('school')->orderByDesc('created_at');

        // Kepala sekolah hanya lihat sekolahnya
        $school = active_school();
        if ($school && !auth()->user()->hasRole('super_admin')) {
            $query->where('school_id', $school->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', RegistrationConstant::PENDING);
        }

        $registrations = $query->paginate(15)->withQueryString();

        return view('super-admin.registrations.index', compact('registrations'));
    }

    public function approve(UserRegistration $registration)
    {
        if ($registration->status !== RegistrationConstant::PENDING) {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        if (User::where('email', $registration->email)->exists()) {
            return back()->with('error', 'Email sudah dipakai akun lain.');
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'              => $registration->name,
                'email'             => $registration->email,
                'password'          => $registration->password, // sudah hashed
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            $user->assignRole(RoleConstant::getSpatieRole($registration->role));

            $identifier = $registration->data['identifier'] ?? null;

            if ($registration->role === RoleConstant::SISWA) {
                $student = Student::where('school_id', $registration->school_id)
                    ->where(fn($q) => $q->where('nis', $identifier)->orWhere('nisn', $identifier))
                    ->first();
                if ($student && !$student->user_id) {
                    $student->update(['user_id' => $user->id]);
                }
            } elseif ($registration->role === RoleConstant::ORANG_TUA) {
                $student = Student::where('school_id', $registration->school_id)
                    ->where(fn($q) => $q->where('nis', $identifier)->orWhere('nisn', $identifier))
                    ->first();
                if ($student) {
                    StudentParent::create([
                        'student_id' => $student->id,
                        'user_id'    => $user->id,
                        'full_name'  => $registration->name,
                        'relation'   => ParentRelationConstant::WALI,
                        'email'      => $registration->email,
                        'is_primary' => $student->parents()->count() === 0,
                    ]);
                }
            }

            $registration->update([
                'status'      => RegistrationConstant::APPROVED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', "Pendaftaran {$registration->name} disetujui & akun dibuat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, UserRegistration $registration)
    {
        $registration->update([
            'status'      => RegistrationConstant::REJECTED,
            'notes'       => $request->input('notes'),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran ditolak.');
    }
}
