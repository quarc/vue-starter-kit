<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    /**
     * Show the notifications page.
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        return Inertia::render('notifications/Index', [
            'notifications' => Inertia::scroll(fn () => $user->notifications()
                ->whereNull('archived_at')
                ->simplePaginate(10)
                ->through(fn ($notification) => NotificationResource::make($notification)->toArray($request))
            ),
            'counts' => $this->notificationCounts($user),
        ]);
    }

    /**
     * Show the archive page.
     */
    public function archive(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $notifications = Inertia::scroll(fn () => $user->notifications()
            ->whereNotNull('archived_at')
            ->latest('archived_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->through(fn ($notification) => NotificationResource::make($notification)->toArray($request))
        );

        return Inertia::render('notifications/Archive', [
            'notifications' => $notifications,
            'counts' => $this->notificationCounts($user),
        ]);
    }

    /**
     * Aggregate notification counts for the authenticated user.
     */
    protected function notificationCounts(User $user): array
    {
        $baseQuery = $user->notifications();

        $total = (clone $baseQuery)->count();
        $unread = (clone $baseQuery)->whereNull('read_at')->count();
        $archived = (clone $baseQuery)->whereNotNull('archived_at')->count();

        return [
            'total' => $total,
            'unread' => $unread,
            'archived' => $archived,
        ];
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsUnread();

        return back();
    }

    /**
     * Archive the notification.
     */
    public function archiveNotification(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $notification->update(['archived_at' => now()]);

        return back();
    }

    /**
     * Unarchive the notification.
     */
    public function unarchiveNotification(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsUnread();
        $notification->update(['archived_at' => null]);

        return back();
    }
}
