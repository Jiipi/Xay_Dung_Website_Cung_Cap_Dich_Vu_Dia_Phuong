<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonViHanhChinh extends Model
{
    protected $table = 'don_vi_hanh_chinh';

    protected $fillable = [
        'ten',
        'cap_don_vi',
        'parent_id',
        'ma_hanh_chinh',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
