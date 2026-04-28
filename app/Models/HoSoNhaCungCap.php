<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class HoSoNhaCungCap extends Model
{
    use HasFactory;

    protected $table = 'ho_so_nha_cung_cap';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'ten_thuong_hieu',
        'gioi_thieu',
        'nam_kinh_nghiem',
        'website',
        'facebook',
        'giay_phep_kinh_doanh',
        'stk_ngan_hang',
        'ten_ngan_hang',
        'ten_chu_tk',
        'ty_le_hoa_hong',
        'diem_danh_gia',
    ];

    protected function casts(): array
    {
        return [
            'ty_le_hoa_hong' => 'decimal:2',
            'diem_danh_gia' => 'decimal:2',
        ];
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function dichVu(): HasMany
    {
        return $this->hasMany(DichVu::class, 'nha_cung_cap_id');
    }

    public function donDatLich(): HasMany
    {
        return $this->hasMany(DonDatLich::class, 'nha_cung_cap_id');
    }

    public function danhGia(): HasMany
    {
        return $this->hasMany(DanhGia::class, 'nha_cung_cap_id');
    }
}
