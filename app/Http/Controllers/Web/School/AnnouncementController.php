<?php

namespace App\Http\Controllers\Web\School;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $query = Announcement::where('school_id', $school->id)
            ->with('createdBy')
            ->withCount('comments');

        if ($request->filled('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }

        $announcements = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('school.announcements.index', compact('announcements', 'school'));
    }

    public function create()
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        return view('school.announcements.create', compact('school'));
    }

    public function store(Request $request)
    {
        $school = active_school();
        if (!$school) return redirect()->route('select.school');

        $data = $this->validateData($request);

        $announcement = Announcement::create([
            'school_id'    => $school->id,
            'title'        => $data['title'],
            'content'      => $data['content'],
            'target_roles' => $data['target_roles'] ?? [],
            'is_public'    => $request->boolean('is_public'),
            'show_in_feed' => $request->boolean('show_in_feed'),
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? now() : null,
            'created_by'   => auth()->id(),
        ]);

        if ($request->hasFile('attachment')) {
            $path = FileHelper::upload($request->file('attachment'), 'document', $announcement->id);
            $announcement->update(['attachment' => $path]);
        }

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorizeSchool($announcement);
        $school = active_school();
        return view('school.announcements.edit', compact('announcement', 'school'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeSchool($announcement);
        $data = $this->validateData($request);

        $announcement->update([
            'title'        => $data['title'],
            'content'      => $data['content'],
            'target_roles' => $data['target_roles'] ?? [],
            'is_public'    => $request->boolean('is_public'),
            'show_in_feed' => $request->boolean('show_in_feed'),
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? ($announcement->published_at ?? now()) : null,
        ]);

        if ($request->hasFile('attachment')) {
            FileHelper::delete($announcement->attachment);
            $path = FileHelper::upload($request->file('attachment'), 'document', $announcement->id);
            $announcement->update(['attachment' => $path]);
        }

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function togglePublish(Announcement $announcement)
    {
        $this->authorizeSchool($announcement);
        $announcement->update([
            'is_published' => !$announcement->is_published,
            'published_at' => !$announcement->is_published ? now() : $announcement->published_at,
        ]);
        return back()->with('success', 'Status publikasi diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorizeSchool($announcement);
        FileHelper::delete($announcement->attachment);
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'target_roles'   => 'nullable|array',
            'target_roles.*' => 'string',
            'attachment'     => 'nullable|file|max:5120',
        ]);
    }

    protected function authorizeSchool(Announcement $announcement): void
    {
        $school = active_school();
        abort_if(!$school || $announcement->school_id !== $school->id, 403);
    }
}
