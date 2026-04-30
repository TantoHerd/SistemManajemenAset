<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for current user
     */
    public function index(Request $request)
    {
        $notifications = Notification::forUser(auth()->id())
            ->latest()
            ->paginate(20);

        $unreadCount = Notification::forUser(auth()->id())
            ->unread()
            ->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread notifications (AJAX)
     */
    public function getUnread()
    {
        $notifications = Notification::forUser(auth()->id())
            ->unread()
            ->latest()
            ->limit(10)
            ->get();

        $count = $notifications->count();

        return response()->json([
            'count' => $count,
            'notifications' => $notifications->map(function($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'icon' => $n->icon,
                    'color' => $n->color,
                    'link' => $n->link,
                    'time' => $n->created_at->diffForHumans(),
                    'is_read' => $n->is_read
                ];
            })
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response()->json(['success' => true]);
    }
}