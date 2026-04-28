<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonDatLich extends Model
{
    use HasFactory;

    protected $table = 'don_dat_lich';

    protected $fillable = [
        'ma_don',
        'khach_hang_id',
        'nha_cung_cap_id',
        'dich_vu_id',
        'thoi_gian_thuc_hien',
        'so_luong',
        'don_vi',
        'dia_diem_thuc_hien',
        'ghi_chu',
        'ma_khuyen_mai',
        'tam_tinh',
        'phi_dich_vu',
        'giam_gia',
        'tong_tien',
        'trang_thai_don',
        'phuong_thuc_thanh_toan',
        'trang_thai_thanh_toan',
        'ma_giao_dich_doi_tac',
        'huy_boi',
        'ly_do_huy',
    ];

    protected function casts(): array
    {
        return [
            'thoi_gian_thuc_hien' => 'datetime',
            'so_luong' => 'decimal:2',
            'tam_tinh' => 'decimal:2',
            'phi_dich_vu' => 'decimal:2',
            'giam_gia' => 'decimal:2',
            'tong_tien' => 'decimal:2',
        ];
    }

    public function khachHang(): BelongsTo
    {
        return $this->belongsTo(User::class, 'khach_hang_id');
    }

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nha_cung_cap_id');
    }

    public function dichVu(): BelongsTo
    {
        return $this->belongsTo(DichVu::class, 'dich_vu_id');
    }

    public function danhGia(): HasOne
    {
        return $this->hasOne(DanhGia::class, 'don_dat_lich_id');
    }
}
