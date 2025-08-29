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

        $staticCategories = [
            'en' => 'New book',
            'ar' => 'كتاب جديد'
        ];

        $notifications = $user->notifications()->latest()->get()->map(function ($notification) use ($locale, $staticCategories) {
            return [
                'id'          => $notification->id,
                'title'       => $notification->data['title'][$locale] ?? $notification->data['title']['en'],
                'category'    => $staticCategories[$locale] ?? $staticCategories['en'],
                'description' => $notification->data['message'][$locale] ?? $notification->data['message']['en'] ?? null,
                'type'        => $notification->data['type'] ?? 'general',
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
