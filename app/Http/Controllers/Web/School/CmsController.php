<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Banner;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    public function index()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $stats = [
            'posts'     => Post::where('school_id', $school->id)->count(),
            'banners'   => Banner::where('school_id', $school->id)->count(),
            'galleries' => Gallery::where('school_id', $school->id)->count(),
        ];

        return view('school.cms.index', compact('school', 'stats'));
    }

    // ── PROFIL SEKOLAH ─────────────────────────────────
    public function profile()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $profile = SchoolProfile::firstOrNew(['school_id' => $school->id]);
        return view('school.cms.profile', compact('school', 'profile'));
    }

    public function profileUpdate(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $data = $request->validate([
            'tagline'        => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'vision'         => 'nullable|string',
            'mission'        => 'nullable|string',
            'history'        => 'nullable|string',
            'founded_year'   => 'nullable|integer|min:1900|max:2100',
            'facebook_url'   => 'nullable|string|max:255',
            'instagram_url'  => 'nullable|string|max:255',
            'youtube_url'    => 'nullable|string|max:255',
            'maps_embed'     => 'nullable|string',
        ]);

        SchoolProfile::updateOrCreate(['school_id' => $school->id], $data);

        return back()->with('success', 'Profil sekolah berhasil disimpan.');
    }

    // ── BERITA / POSTS ─────────────────────────────────
    public function posts()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $posts = Post::where('school_id', $school->id)->with('category')
            ->orderByDesc('created_at')->paginate(12);
        $categories = PostCategory::where('school_id', $school->id)->orderBy('name')->get();

        return view('school.cms.posts', compact('school', 'posts', 'categories'));
    }

    public function postCreate()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');
        $categories = PostCategory::where('school_id', $school->id)->orderBy('name')->get();
        $post = null;
        return view('school.cms.post-form', compact('school', 'categories', 'post'));
    }

    public function postStore(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $data = $this->validatePost($request);
        $post = Post::create([
            'school_id'    => $school->id,
            'category_id'  => $data['category_id'] ?? null,
            'title'        => $data['title'],
            'slug'         => $this->uniqueSlug($school->id, $data['title']),
            'excerpt'      => $data['excerpt'] ?? null,
            'content'      => $data['content'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'show_in_feed' => $request->boolean('show_in_feed'),
            'published_at' => $request->boolean('is_published') ? now() : null,
            'created_by'   => auth()->id(),
        ]);

        if ($request->hasFile('thumbnail')) {
            $post->update(['thumbnail' => FileHelper::upload($request->file('thumbnail'), 'post_thumbnail', $post->id)]);
        }

        return redirect()->route('cms.posts')->with('success', 'Berita berhasil dibuat.');
    }

    public function postEdit(string $post)
    {
        $post = $this->findPost($post);
        $school = active_school();
        $categories = PostCategory::where('school_id', $school->id)->orderBy('name')->get();
        return view('school.cms.post-form', compact('school', 'categories', 'post'));
    }

    public function postUpdate(Request $request, string $post)
    {
        $post = $this->findPost($post);
        $data = $this->validatePost($request);

        $post->update([
            'category_id'  => $data['category_id'] ?? null,
            'title'        => $data['title'],
            'excerpt'      => $data['excerpt'] ?? null,
            'content'      => $data['content'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'show_in_feed' => $request->boolean('show_in_feed'),
            'published_at' => $request->boolean('is_published') ? ($post->published_at ?? now()) : null,
        ]);

        if ($request->hasFile('thumbnail')) {
            FileHelper::delete($post->thumbnail);
            $post->update(['thumbnail' => FileHelper::upload($request->file('thumbnail'), 'post_thumbnail', $post->id)]);
        }

        return redirect()->route('cms.posts')->with('success', 'Berita berhasil diperbarui.');
    }

    public function postDestroy(string $post)
    {
        $post = $this->findPost($post);
        FileHelper::delete($post->thumbnail);
        $post->delete();
        return back()->with('success', 'Berita berhasil dihapus.');
    }

    public function categoryStore(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');
        $request->validate(['name' => 'required|string|max:100']);
        PostCategory::create([
            'school_id' => $school->id,
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
        ]);
        return back()->with('success', 'Kategori ditambahkan.');
    }

    // ── BANNER ─────────────────────────────────────────
    public function banners()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');
        $banners = Banner::where('school_id', $school->id)->orderBy('order')->get();
        return view('school.cms.banners', compact('school', 'banners'));
    }

    public function bannerStore(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $request->validate([
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
            'button_text' => 'nullable|string|max:50',
            'button_url'  => 'nullable|string|max:255',
        ]);

        $banner = Banner::create([
            'school_id'   => $school->id,
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'image'       => 'pending',
            'button_text' => $request->button_text,
            'button_url'  => $request->button_url,
            'order'       => Banner::where('school_id', $school->id)->count(),
            'is_published'=> true,
        ]);
        $banner->update(['image' => FileHelper::upload($request->file('image'), 'school_banner', $banner->id)]);

        return back()->with('success', 'Banner ditambahkan.');
    }

    public function bannerDestroy(string $banner)
    {
        $banner = $this->findBanner($banner);
        FileHelper::delete($banner->image);
        $banner->delete();
        return back()->with('success', 'Banner dihapus.');
    }

    // ── GALERI ─────────────────────────────────────────
    public function galleries()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');
        $galleries = Gallery::where('school_id', $school->id)->withCount('items')->latest()->get();
        return view('school.cms.galleries', compact('school', 'galleries'));
    }

    public function galleryStore(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');
        $request->validate(['title' => 'required|string|max:255', 'description' => 'nullable|string']);
        Gallery::create([
            'school_id'    => $school->id,
            'title'        => $request->title,
            'description'  => $request->description,
            'is_published' => true,
            'published_at' => now(),
            'created_by'   => auth()->id(),
        ]);
        return back()->with('success', 'Album galeri dibuat.');
    }

    public function galleryShow(string $gallery)
    {
        $gallery = $this->findGallery($gallery);
        $school = active_school();
        $gallery->load('items');
        return view('school.cms.gallery-show', compact('school', 'gallery'));
    }

    public function galleryItemStore(Request $request, string $gallery)
    {
        $gallery = $this->findGallery($gallery);
        $request->validate(['images.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072']);

        foreach ((array) $request->file('images', []) as $file) {
            $item = GalleryItem::create(['gallery_id' => $gallery->id, 'type' => 1, 'file_path' => 'pending']);
            $item->update(['file_path' => FileHelper::upload($file, 'gallery_item', $item->id)]);
            if (!$gallery->thumbnail) $gallery->update(['thumbnail' => $item->file_path]);
        }
        return back()->with('success', 'Foto ditambahkan ke galeri.');
    }

    public function galleryItemDestroy(string $item)
    {
        $item = $this->findGalleryItem($item);
        FileHelper::delete($item->file_path);
        $item->delete();
        return back()->with('success', 'Foto dihapus.');
    }

    public function galleryDestroy(string $gallery)
    {
        $gallery = $this->findGallery($gallery);
        foreach ($gallery->items as $item) FileHelper::delete($item->file_path);
        $gallery->delete();
        return redirect()->route('cms.galleries')->with('success', 'Album galeri dihapus.');
    }

    // ── Helpers ────────────────────────────────────────
    protected function validatePost(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:post_categories,id',
            'excerpt'     => 'nullable|string|max:500',
            'content'     => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);
    }

    protected function uniqueSlug(int $schoolId, string $title): string
    {
        $base = Str::slug($title); $slug = $base; $i = 1;
        while (Post::where('school_id', $schoolId)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    protected function findPost(string $hash): Post
    {
        $post = Post::findOrFail(hashid_decode_or_404($hash, Post::class));
        $this->authorizePost($post);
        return $post;
    }

    protected function findBanner(string $hash): Banner
    {
        $banner = Banner::findOrFail(hashid_decode_or_404($hash, Banner::class));
        $school = active_school();
        abort_if(!$school || $banner->school_id !== $school->id, 403);
        return $banner;
    }

    protected function findGallery(string $hash): Gallery
    {
        $gallery = Gallery::findOrFail(hashid_decode_or_404($hash, Gallery::class));
        $school = active_school();
        abort_if(!$school || $gallery->school_id !== $school->id, 403);
        return $gallery;
    }

    protected function findGalleryItem(string $hash): GalleryItem
    {
        $item = GalleryItem::with('gallery')->findOrFail(hashid_decode_or_404($hash, GalleryItem::class));
        $school = active_school();
        abort_if(!$school || !$item->gallery || $item->gallery->school_id !== $school->id, 403);
        return $item;
    }

    protected function authorizePost(Post $post): void
    {
        $school = active_school();
        abort_if(!$school || $post->school_id !== $school->id, 403);
    }
}
