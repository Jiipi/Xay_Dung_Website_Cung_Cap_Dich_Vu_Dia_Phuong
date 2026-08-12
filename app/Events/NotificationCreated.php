<?php

namespace App\Events;

use App\Models\ThongBao;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ThongBao $notification,
        public int $unreadCount
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.' . $this->notification->nguoi_dung_id)];
    }

    public function broadcastWith(): array
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'title' => $this->notification->tieu_de,
                'body' => $this->notification->noi_dung,
                'type' => $this->notification->loai_thong_bao,
                'read' => (bool) $this->notification->da_doc,
                'date' => $this->notification->created_at?->diffForHumans(),
            ],
            'unreadNotifications' => $this->unreadCount,
        ];
    }
}
