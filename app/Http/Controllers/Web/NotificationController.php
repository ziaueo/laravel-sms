<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at')->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification->update(['is_read' => true, 'read_at' => now()]);
        return back();
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function destroy(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification->delete();
        return back()->with('success', 'Notifikasi dihapus.');
    }
}
