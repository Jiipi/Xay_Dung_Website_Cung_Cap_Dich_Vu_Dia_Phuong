<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $table = 'nguoi_dung';

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ho_ten',
        'email',
        'so_dien_thoai',
        'mat_khau_hash',
        'vai_tro',
        'trang_thai',
        'anh_dai_dien',
        'dia_chi_chi_tiet',
        'phuong_xa_id',
        'quan_huyen_id',
        'tinh_thanh_id',
        'ngay_sinh',
        'gioi_tinh',
        'toa_do_lat',
        'toa_do_lng',
        'lan_dang_nhap_cuoi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'mat_khau_hash',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the user's display name.
     */
    protected function name(): Attribute
    {
        return Attribute::get(fn (mixed $value, array $attributes): ?string => $attributes['ho_ten'] ?? null);
    }

    /**
     * Expose the hashed password through the default attribute name used by tests.
     */
    protected function password(): Attribute
    {
        return Attribute::get(fn (mixed $value, array $attributes): ?string => $attributes['mat_khau_hash'] ?? null);
    }

    /**
     * Tell Laravel which column stores the authentication password hash.
     */
    public function getAuthPasswordName(): string
    {
        return 'mat_khau_hash';
    }

    /**
     * Get the column name for the "email verified at" timestamp.
     */
    public function getEmailVerifiedAtColumn(): string
    {
        return 'email_da_xac_minh';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_da_xac_minh' => 'datetime',
            'mat_khau_hash' => 'hashed',
            'lan_dang_nhap_cuoi' => 'datetime',
            'ngay_sinh' => 'date',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // --- Relationships ---

    public function vaiTroNguoiDung(): BelongsTo
    {
        return $this->belongsTo(VaiTroNguoiDung::class, 'vai_tro');
    }

    public function phuongXa(): BelongsTo
    {
        return $this->belongsTo(DonViHanhChinh::class, 'phuong_xa_id');
    }

    public function quanHuyen(): BelongsTo
    {
        return $this->belongsTo(DonViHanhChinh::class, 'quan_huyen_id');
    }

    public function tinhThanh(): BelongsTo
    {
        return $this->belongsTo(DonViHanhChinh::class, 'tinh_thanh_id');
    }

    public function hoSoNhaCungCap(): HasOne
    {
        return $this->hasOne(HoSoNhaCungCap::class, 'id');
    }

    public function donDatLichKhachHang(): HasMany
    {
        return $this->hasMany(DonDatLich::class, 'khach_hang_id');
    }

    public function donDatLichNhaCungCap(): HasMany
    {
        return $this->hasMany(DonDatLich::class, 'nha_cung_cap_id');
    }

    public function danhGia(): HasMany
    {
        return $this->hasMany(DanhGia::class, 'khach_hang_id');
    }

    public function thongBao(): HasMany
    {
        return $this->hasMany(ThongBao::class, 'nguoi_dung_id');
    }

    public function yeuThich(): HasMany
    {
        return $this->hasMany(YeuThich::class, 'nguoi_dung_id');
    }

    public function dichVu(): HasMany
    {
        return $this->hasMany(DichVu::class, 'nha_cung_cap_id');
    }
}
