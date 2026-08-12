<?php

namespace App\Events;

use App\Models\DonDatLich;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public DonDatLich $booking,
        public int $userId,
        public int $pendingBookingsCount
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.' . $this->userId)];
    }

    public function broadcastWith(): array
    {
        return [
            'booking' => [
                'id' => $this->booking->id,
                'ma_don' => $this->booking->ma_don,
                'trang_thai_don' => $this->booking->trang_thai_don,
                'trang_thai_thanh_toan' => $this->booking->trang_thai_thanh_toan,
                'khach_hang_id' => $this->booking->khach_hang_id,
                'nha_cung_cap_id' => $this->booking->nha_cung_cap_id,
            ],
            'pendingBookingsCount' => $this->pendingBookingsCount,
        ];
    }
}
