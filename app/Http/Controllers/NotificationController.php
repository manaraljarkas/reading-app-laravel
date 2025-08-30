<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationModel;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $locale = $request->header('lang', app()->getLocale());

        $notifications = $user->notifications()->latest()->get()->map(function ($notification) use ($locale) {
            $titleData = $notification->data['title'] ?? [];
            $messageData = $notification->data['message'] ?? [];
            $categoryData = json_decode($notification->category, true) ?? [];

            return [
                'id'          => $notification->id,
                'title'       => $titleData[$locale] ?? $titleData['en'] ?? null,
                'category'    => $categoryData[$locale] ?? $categoryData['en'] ?? null,
                'description' => $messageData[$locale] ?? $messageData['en'] ?? null,
                'is_read'     => $notification->read_at ? true : false,
                'created_at'  => $notification->created_at->toDateTimeString(),
            ];
        });


        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications
        ]);
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();
        return response()->json(['count' => $user->unreadNotifications()->count()]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $notification = NotificationModel::where('id', $id)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }
}
