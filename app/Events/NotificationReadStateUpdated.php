<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationReadStateUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public int $unreadCount,
        public ?int $notificationId = null
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.' . $this->userId)];
    }

    public function broadcastWith(): array
    {
        return [
            'notificationId' => $this->notificationId,
            'unreadNotifications' => $this->unreadCount,
        ];
    }
}
