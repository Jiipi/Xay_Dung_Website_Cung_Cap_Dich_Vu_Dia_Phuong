<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DichVu extends Model
{
    use HasFactory;

    protected $table = 'dich_vu';

    protected $fillable = [
        'nha_cung_cap_id',
        'danh_muc_id',
        'ten_dich_vu',
        'slug',
        'mo_ta_chi_tiet',
        'gia_tu',
        'gia_den',
        'don_vi_gia',
        'dia_chi_hien_thi',
        'tinh_thanh_id',
        'quan_huyen_id',
        'toa_do_lat',
        'toa_do_lng',
        'danh_sach_anh',
        'the_tu_khoa',
        'khu_vuc_phuc_vu',
        'thuoc_tinh',
        'lich_lam_viec',
        'do_uu_tien',
        'trang_thai_duyet',
        'trang_thai_hoat_dong',
    ];

    protected function casts(): array
    {
        return [
            'danh_sach_anh' => 'array',
            'the_tu_khoa' => 'array',
            'khu_vuc_phuc_vu' => 'array',
            'thuoc_tinh' => 'array',
            'lich_lam_viec' => 'array',
            'gia_tu' => 'decimal:2',
            'gia_den' => 'decimal:2',
        ];
    }

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nha_cung_cap_id');
    }

    public function danhMuc(): BelongsTo
    {
        return $this->belongsTo(DanhMucDichVu::class, 'danh_muc_id');
    }

    public function tinhThanh(): BelongsTo
    {
        return $this->belongsTo(DonViHanhChinh::class, 'tinh_thanh_id');
    }

    public function quanHuyen(): BelongsTo
    {
        return $this->belongsTo(DonViHanhChinh::class, 'quan_huyen_id');
    }

    public function donDatLich(): HasMany
    {
        return $this->hasMany(DonDatLich::class, 'dich_vu_id');
    }

    public function yeuThich(): HasMany
    {
        return $this->hasMany(YeuThich::class, 'dich_vu_id');
    }
}
