<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Post;
use App\Models\Gallery;
use App\Models\Banner;
use App\Models\Announcement;
use App\Models\PpdbPeriod;
use App\Models\PpdbRegistration;
use App\Models\Teacher;
use App\Constants\PpdbConstant;
use App\Constants\PositionConstant;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    protected function resolveSchool(string $slug): School
    {
        return School::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function home(string $slug)
    {
        $school = $this->resolveSchool($slug);
        $school->load('profile');

        $banners = Banner::where('school_id', $school->id)->where('is_published', true)
            ->orderBy('order')->get();

        $posts = Post::where('school_id', $school->id)->where('is_published', true)
            ->orderByDesc('published_at')->orderByDesc('created_at')->limit(3)->get();

        $galleries = Gallery::where('school_id', $school->id)->where('is_published', true)
            ->latest()->limit(6)->get();

        $announcements = Announcement::where('school_id', $school->id)
            ->where('is_published', true)->where('is_public', true)
            ->orderByDesc('published_at')->limit(3)->get();

        return view('public.home', compact('school', 'banners', 'posts', 'galleries', 'announcements'));
    }

    public function profil(string $slug)
    {
        $school = $this->resolveSchool($slug);
        $school->load('profile', 'schoolType');
        return view('public.profil', compact('school'));
    }

    public function guru(string $slug)
    {
        $school = $this->resolveSchool($slug);
        $school->load('profile');

        $teachers = Teacher::where('school_id', $school->id)
            ->where('is_active', true)
            ->with('position')
            ->get()
            // Kunci gabungan: urutan jabatan dulu, lalu nama. Digabung jadi satu
            // string supaya hasilnya tidak bergantung pada kestabilan sort PHP.
            ->sortBy(fn ($t) => sprintf('%03d|%s', $t->position?->order ?? 999, $t->full_name))
            ->values();

        $byType     = $teachers->groupBy(fn ($t) => $t->position?->type ?? 0);
        $leadership = $byType->get(PositionConstant::PIMPINAN, collect());

        // Pemegang jabatan dengan order terkecil — bukan dicocokkan dari nama jabatan,
        // supaya tidak patah kalau penamaannya berubah.
        $headmaster = $leadership->first();

        $sections = [
            ['title' => 'Pimpinan Sekolah',    'people' => $leadership->skip(1)->values()],
            ['title' => 'Guru',                'people' => $byType->get(PositionConstant::GURU, collect())],
            ['title' => 'Tenaga Kependidikan', 'people' => $byType->get(PositionConstant::STAFF, collect())],
            // Jabatan belum diisi — teachers.position_id memang nullable.
            ['title' => 'Lainnya',             'people' => $byType->get(0, collect())],
        ];

        return view('public.guru', compact('school', 'teachers', 'headmaster', 'sections'));
    }

    public function berita(string $slug)
    {
        $school = $this->resolveSchool($slug);
        $school->load('profile');

        $posts = Post::where('school_id', $school->id)->where('is_published', true)
            ->with('category')
            ->orderByDesc('published_at')->orderByDesc('created_at')
            ->paginate(9);

        return view('public.berita', compact('school', 'posts'));
    }

    public function beritaDetail(string $slug, string $postSlug)
    {
        $school = $this->resolveSchool($slug);
        $school->load('profile');

        $post = Post::where('school_id', $school->id)->where('slug', $postSlug)
            ->where('is_published', true)->with('category', 'createdBy')->firstOrFail();

        $related = Post::where('school_id', $school->id)->where('is_published', true)
            ->where('id', '!=', $post->id)->latest()->limit(3)->get();

        return view('public.berita-detail', compact('school', 'post', 'related'));
    }

    public function galeri(string $slug)
    {
        $school = $this->resolveSchool($slug);
        $school->load('profile');

        $galleries = Gallery::where('school_id', $school->id)->where('is_published', true)
            ->with('items')->latest()->paginate(12);

        return view('public.galeri', compact('school', 'galleries'));
    }

    public function kontak(string $slug)
    {
        $school = $this->resolveSchool($slug);
        $school->load('profile');
        return view('public.kontak', compact('school'));
    }

    public function ppdb(string $slug)
    {
        $school = $this->resolveSchool($slug);
        $school->load('profile');

        $period = PpdbPeriod::where('school_id', $school->id)->where('is_active', true)
            ->orderByDesc('open_date')->get()
            ->first(fn($p) => $p->is_open);

        return view('public.ppdb', compact('school', 'period'));
    }

    public function ppdbStore(Request $request, string $slug)
    {
        $school = $this->resolveSchool($slug);

        $period = PpdbPeriod::where('school_id', $school->id)->where('is_active', true)
            ->orderByDesc('open_date')->get()->first(fn($p) => $p->is_open);

        if (!$period) {
            return back()->with('error', 'Maaf, pendaftaran sedang ditutup.');
        }

        $data = $request->validate([
            'full_name'       => 'required|string|max:255',
            'gender'          => 'required|integer|in:1,2',
            'birth_place'     => 'nullable|string|max:100',
            'birth_date'      => 'nullable|date',
            'religion'        => 'nullable|string|max:50',
            'address'         => 'nullable|string',
            'previous_school' => 'nullable|string|max:255',
            'parent_name'     => 'required|string|max:255',
            'parent_relation' => 'required|integer|in:1,2,3',
            'parent_phone'    => 'required|string|max:20',
            'parent_email'    => 'nullable|email|max:255',
            'parent_job'      => 'nullable|string|max:100',
        ]);

        $number = 'PPDB-' . $school->id . '-' . date('Ymd') . '-' . str_pad((string)(PpdbRegistration::where('ppdb_period_id', $period->id)->count() + 1), 4, '0', STR_PAD_LEFT);

        PpdbRegistration::create(array_merge($data, [
            'school_id'           => $school->id,
            'school_year_id'      => $period->school_year_id,
            'ppdb_period_id'      => $period->id,
            'registration_number' => $number,
            'status'              => PpdbConstant::PENDING,
        ]));

        return redirect()->route('public.ppdb', $slug)
            ->with('success', "Pendaftaran berhasil! Nomor pendaftaran Anda: {$number}. Simpan nomor ini.");
    }
}
