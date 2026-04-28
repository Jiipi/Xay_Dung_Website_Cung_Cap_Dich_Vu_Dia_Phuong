<?php

namespace App\Services\Notification;

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
        return (bool) ThongBao::where('nguoi_dung_id', $userId)
            ->where('id', $notificationId)
            ->update(['da_doc' => true]);
    }

    public function markAllAsRead(int $userId): bool
    {
        return (bool) ThongBao::where('nguoi_dung_id', $userId)
            ->where('da_doc', false)
            ->update(['da_doc' => true]);
    }

    public function createNotification(array $data): ThongBao
    {
        return ThongBao::create($data);
    }
}
