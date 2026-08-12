<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'khach_hang_id',
        'nha_cung_cap_id',
        'don_dat_lich_id',
    ];

    public function khachHang()
    {
        return $this->belongsTo(User::class, 'khach_hang_id');
    }

    public function nhaCungCap()
    {
        return $this->belongsTo(User::class, 'nha_cung_cap_id');
    }

    public function donDatLich()
    {
        return $this->belongsTo(DonDatLich::class, 'don_dat_lich_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadCountFor(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }
}
