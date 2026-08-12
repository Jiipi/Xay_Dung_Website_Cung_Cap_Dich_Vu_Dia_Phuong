<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauRutTien extends Model
{
    protected $table = 'yeu_cau_rut_tien';

    protected $fillable = [
        'nguoi_dung_id',
        'giao_dich_id',
        'so_tien',
        'trang_thai',
        'ghi_chu',
        'admin_ghi_chu',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function giaoDich()
    {
        return $this->belongsTo(GiaoDich::class, 'giao_dich_id');
    }
}
