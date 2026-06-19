<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\School;
use App\Models\UserSchool;
use App\Constants\RoleConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    const DEFAULT_PASSWORD = 'P@ssw0rd';

    public function index(Request $request)
    {
        $activeTab = (int) $request->get('tab', RoleConstant::KEPALA_SEKOLAH);

        $schools = $this->getAccessibleSchools();

        $query = User::query()
            ->whereHas('userSchools', function ($q) use ($activeTab) {
                $q->where('role', $activeTab);
            })
            ->with(['userSchools' => function ($q) use ($activeTab) {
                $q->where('role', $activeTab)->with('school');
            }]);

        // Filter sekolah (jika bukan super admin, hanya sekolah miliknya)
        if (!auth()->user()->hasRole('super_admin')) {
            $schoolIds = $schools->pluck('id');
            $query->whereHas('userSchools', function ($q) use ($schoolIds, $activeTab) {
                $q->where('role', $activeTab)->whereIn('school_id', $schoolIds);
            });
        }

        // Filter by sekolah tertentu
        if ($request->filled('school_id')) {
            $query->whereHas('userSchools', function ($q) use ($request, $activeTab) {
                $q->where('role', $activeTab)->where('school_id', $request->school_id);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        $roleCounts = $this->getRoleCounts($schools);

        return view('super-admin.users.index', compact('users', 'schools', 'activeTab', 'roleCounts'));
    }

    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name'                  => $request->name,
                'email'                 => $request->email,
                'password'              => Hash::make(self::DEFAULT_PASSWORD),
                'phone'                 => $request->phone,
                'is_active'             => true,
                'must_change_password'  => true,
                'registered_by'         => auth()->id(),
                'email_verified_at'     => now(),
            ]);

            $roleName = RoleConstant::getSpatieRole((int) $request->role);
            $user->assignRole($roleName);

            foreach ($request->school_ids as $schoolId) {
                UserSchool::create([
                    'user_id'   => $user->id,
                    'school_id' => $schoolId,
                    'role'      => $request->role,
                ]);
            }

            DB::commit();

            return back()->with('success', "User \"{$user->name}\" berhasil ditambahkan dengan password default: " . self::DEFAULT_PASSWORD);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Update relasi sekolah — role tetap sama, hanya sekolahnya yang diupdate
            $existingRole = $user->userSchools()->first()?->role;

            if ($existingRole) {
                $user->userSchools()->where('role', $existingRole)->delete();

                foreach ($request->school_ids as $schoolId) {
                    UserSchool::create([
                        'user_id'   => $user->id,
                        'school_id' => $schoolId,
                        'role'      => $existingRole,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', "User \"{$user->name}\" berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User \"{$user->name}\" berhasil {$status}.");
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password'              => Hash::make(self::DEFAULT_PASSWORD),
            'must_change_password'  => true,
        ]);

        return back()->with('success', "Password \"{$user->name}\" berhasil direset ke default: " . self::DEFAULT_PASSWORD);
    }

    public function destroy(User $user)
    {
        $name = $user->name;
        $user->userSchools()->delete();
        $user->delete();

        return back()->with('success', "User \"{$name}\" berhasil dihapus.");
    }

    // ── Helper Methods ──────────────────────────────────
    protected function getAccessibleSchools()
    {
        if (auth()->user()->hasRole('super_admin')) {
            return School::where('is_active', true)->orderBy('name')->get();
        }

        return auth()->user()->schools()->where('is_active', true)->orderBy('name')->get();
    }

    protected function getRoleCounts($schools)
    {
        $schoolIds = $schools->pluck('id');
        $isSuperAdmin = auth()->user()->hasRole('super_admin');

        $counts = [];
        foreach (RoleConstant::getAll() as $value => $label) {
            $query = UserSchool::where('role', $value);

            if (!$isSuperAdmin) {
                $query->whereIn('school_id', $schoolIds);
            }

            $counts[$value] = $query->distinct('user_id')->count('user_id');
        }

        return $counts;
    }
}
