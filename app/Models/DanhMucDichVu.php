<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DanhMucDichVu extends Model
{
    protected $table = 'danh_muc_dich_vu';

    protected $fillable = [
        'parent_id',
        'ten_danh_muc',
        'slug',
        'mo_ta',
        'anh_dai_dien',
        'thu_tu_hien_thi',
        'trang_thai',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function dichVu(): HasMany
    {
        return $this->hasMany(DichVu::class, 'danh_muc_id');
    }
}
