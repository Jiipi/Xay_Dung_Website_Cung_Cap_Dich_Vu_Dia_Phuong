<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiaoDich extends Model
{
    protected $table = 'giao_dich';

    protected $fillable = [
        'nguoi_dung_id',
        'don_dat_lich_id',
        'loai_giao_dich',
        'so_tien',
        'phuong_thuc',
        'ma_giao_dich_doi_tac',
        'trang_thai',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'so_tien' => 'decimal:2',
        ];
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function donDatLich()
    {
        return $this->belongsTo(DonDatLich::class, 'don_dat_lich_id');
    }
}
