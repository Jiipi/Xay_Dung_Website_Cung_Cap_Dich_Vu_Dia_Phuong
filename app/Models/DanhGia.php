<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'danh_gia';

    protected $fillable = [
        'don_dat_lich_id',
        'nha_cung_cap_id',
        'khach_hang_id',
        'so_sao',
        'noi_dung',
        'an_danh',
        'phan_hoi_tu_ncc',
        'ngay_phan_hoi',
    ];

    protected function casts(): array
    {
        return [
            'an_danh' => 'boolean',
            'ngay_phan_hoi' => 'datetime',
        ];
    }

    public function donDatLich(): BelongsTo
    {
        return $this->belongsTo(DonDatLich::class, 'don_dat_lich_id');
    }

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nha_cung_cap_id');
    }

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(User::class, 'khach_hang_id');
    }
}
