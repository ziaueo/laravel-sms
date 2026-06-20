<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreSchoolRequest;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\SchoolProfile;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = School::with(['schoolType', 'profile'])
            ->withCount('users');

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        if ($request->filled('school_type_id')) {
            $query->where('school_type_id', $request->school_type_id);
        }

        $schools = $query->orderBy('name')->paginate(9)->withQueryString();
        $schoolTypes = SchoolType::where('is_active', true)->orderBy('order')->get();

        return view('super-admin.schools.index', compact('schools', 'schoolTypes'));
    }

    public function store(StoreSchoolRequest $request)
    {
        DB::beginTransaction();
        try {
            $school = School::create([
                'school_type_id' => $request->school_type_id,
                'name'           => $request->name,
                'slug'           => $this->generateUniqueSlug($request->name),
                'npsn'           => $request->npsn,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'accreditation'  => $request->accreditation,
                'is_active'      => true,
            ]);

            if ($request->hasFile('logo')) {
                $logoPath = FileHelper::upload($request->file('logo'), 'school_logo', $school->id);
                $school->update(['logo' => $logoPath]);
            }

            // Buat profil kosong sekaligus
            SchoolProfile::create(['school_id' => $school->id]);

            DB::commit();

            return back()->with('success', "Sekolah \"{$school->name}\" berhasil ditambahkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan sekolah: ' . $e->getMessage());
        }
    }

    public function update(UpdateSchoolRequest $request, School $school)
    {
        DB::beginTransaction();
        try {
            $school->update([
                'school_type_id' => $request->school_type_id,
                'name'           => $request->name,
                'npsn'           => $request->npsn,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'accreditation'  => $request->accreditation,
                'is_active'      => $request->boolean('is_active', true),
            ]);

            if ($request->hasFile('logo')) {
                FileHelper::delete($school->logo);
                $logoPath = FileHelper::upload($request->file('logo'), 'school_logo', $school->id);
                $school->update(['logo' => $logoPath]);
            }

            // Update / buat profil
            $school->profile()->updateOrCreate(
                ['school_id' => $school->id],
                [
                    'tagline'        => $request->tagline,
                    'description'    => $request->description,
                    'vision'         => $request->vision,
                    'mission'        => $request->mission,
                    'history'        => $request->history,
                    'principal_name' => $request->principal_name,
                    'founded_year'   => $request->founded_year,
                    'facebook_url'   => $request->facebook_url,
                    'instagram_url'  => $request->instagram_url,
                    'youtube_url'    => $request->youtube_url,
                    'tiktok_url'     => $request->tiktok_url,
                    'maps_embed'     => $request->maps_embed,
                ]
            );

            DB::commit();

            return back()->with('success', "Sekolah \"{$school->name}\" berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui sekolah: ' . $e->getMessage());
        }
    }

    public function toggleActive(School $school)
    {
        $school->update(['is_active' => !$school->is_active]);

        $status = $school->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Sekolah \"{$school->name}\" berhasil {$status}.");
    }

    public function destroy(School $school)
    {
        $name = $school->name;
        $school->delete();

        return back()->with('success', "Sekolah \"{$name}\" berhasil dihapus.");
    }

    public function trash(Request $request)
    {
        $query = School::onlyTrashed()->with('schoolType');

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        $schools = $query->orderBy('deleted_at', 'desc')->paginate(9)->withQueryString();

        return view('super-admin.schools.trash', compact('schools'));
    }

    public function restore($id)
    {
        $school = School::onlyTrashed()->findOrFail($id);
        $school->restore();
        $school->update(['is_active' => true]);

        return back()->with('success', "Sekolah \"{$school->name}\" berhasil dipulihkan.");
    }

    public function forceDelete($id)
    {
        $school = School::onlyTrashed()->findOrFail($id);
        $name = $school->name;

        FileHelper::delete($school->logo);
        $school->profile()->delete();
        $school->forceDelete();

        return back()->with('success', "Sekolah \"{$name}\" berhasil dihapus permanen.");
    }

    // ── Helper ──────────────────────────────────────────
    protected function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (School::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
