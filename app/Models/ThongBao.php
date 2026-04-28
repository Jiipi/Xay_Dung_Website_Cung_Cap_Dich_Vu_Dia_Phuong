<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThongBao extends Model
{
    protected $table = 'thong_bao';

    public $timestamps = false;

    protected $fillable = [
        'nguoi_dung_id',
        'tieu_de',
        'noi_dung',
        'loai_thong_bao',
        'da_doc',
    ];

    protected function casts(): array
    {
        return [
            'da_doc' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
