<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProviderNotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}
    /**
     * Provider notification center page.
     */
    public function index(): Response
    {
        $user = auth()->user();

        $notifications = $this->notificationService->getUserNotifications($user->id, 50)
            ->map(fn ($n) => [
                'id'    => $n->id,
                'title' => $n->tieu_de,
                'body'  => $n->noi_dung,
                'type'  => $n->loai_thong_bao,
                'read'  => (bool) $n->da_doc,
                'date'  => $n->created_at?->diffForHumans(),
            ]);

        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        return Inertia::render('provider/notifications/Index', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(int $id): \Illuminate\Http\RedirectResponse
    {
        $this->notificationService->markAsRead(auth()->id(), $id);

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): \Illuminate\Http\RedirectResponse
    {
        $this->notificationService->markAllAsRead(auth()->id());

        return back()->with('success', 'Đã đánh dấu đọc tất cả thông báo.');
    }
}
