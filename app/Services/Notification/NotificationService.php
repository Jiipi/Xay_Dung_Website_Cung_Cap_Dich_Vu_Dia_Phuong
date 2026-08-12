<?php

namespace App\Services\Notification;

use App\Events\NotificationCreated;
use App\Events\NotificationReadStateUpdated;
use App\Models\ThongBao;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function getUserNotifications(int $userId, int $limit = 50): Collection
    {
        return ThongBao::where('nguoi_dung_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getUnreadCount(int $userId): int
    {
        return ThongBao::where('nguoi_dung_id', $userId)
            ->where('da_doc', false)
            ->count();
    }

    public function markAsRead(int $userId, int $notificationId): bool
    {
        $updated = (bool) ThongBao::where('nguoi_dung_id', $userId)
            ->where('id', $notificationId)
            ->update(['da_doc' => true]);

        if ($updated) {
            broadcast(new NotificationReadStateUpdated($userId, $this->getUnreadCount($userId), $notificationId))->toOthers();
        }

        return $updated;
    }

    public function markAllAsRead(int $userId): bool
    {
        $updated = (bool) ThongBao::where('nguoi_dung_id', $userId)
            ->where('da_doc', false)
            ->update(['da_doc' => true]);

        if ($updated) {
            broadcast(new NotificationReadStateUpdated($userId, $this->getUnreadCount($userId)))->toOthers();
        }

        return $updated;
    }

    public function createNotification(array $data): ThongBao
    {
        $notification = ThongBao::create($data);
        broadcast(new NotificationCreated($notification, $this->getUnreadCount($notification->nguoi_dung_id)))->toOthers();

        return $notification;
    }
}
