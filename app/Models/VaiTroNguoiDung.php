<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaiTroNguoiDung extends Model
{
    protected $table = 'vai_tro_nguoi_dung';

    public $timestamps = false;

    protected $fillable = [
        'ten_vai_tro',
        'mo_ta',
        'quyen',
    ];

    public function nguoiDung(): HasMany
    {
        return $this->hasMany(User::class, 'vai_tro');
    }
}
