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
use App\Constants\PpdbConstant;
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
