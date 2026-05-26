<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('notifications')) {
            return view('notifications.index', ['notifications' => collect()]);
        }

        $notifications = auth()->user()->notifications()->latest()->paginate(12);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if (! Schema::hasTable('notifications')) {
            return back()->with('error', 'Notifications are not available yet.');
        }

        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notifikasi ditandai sebagai sudah dibaca.');
    }
}
