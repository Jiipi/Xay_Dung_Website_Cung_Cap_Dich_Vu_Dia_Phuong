<?php

namespace App\Repositories\Eloquent\Booking;

use App\Models\DonDatLich;
use App\Repositories\Contracts\Booking\BookingRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function findById(int $id): ?DonDatLich
    {
        return DonDatLich::find($id);
    }

    public function findByIdAndCustomer(int $id, int $customerId): ?DonDatLich
    {
        return DonDatLich::with(['dichVu', 'nhaCungCap.hoSoNhaCungCap', 'danhGia'])
            ->where('khach_hang_id', $customerId)
            ->find($id);
    }

    public function findByIdAndProvider(int $id, int $providerId): ?DonDatLich
    {
        return DonDatLich::with(['khachHang', 'dichVu', 'danhGia'])
            ->where('nha_cung_cap_id', $providerId)
            ->find($id);
    }

    public function getPaginatedForProvider(int $providerId, ?string $status, ?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $query = DonDatLich::with(['khachHang', 'dichVu'])
            ->where('nha_cung_cap_id', $providerId);

        if ($status) {
            $query->where('trang_thai_don', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_don', 'ilike', "%{$search}%")
                    ->orWhereHas('khachHang', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('ho_ten', 'ilike', "%{$search}%")
                            ->orWhere('so_dien_thoai', 'ilike', "%{$search}%");
                    })
                    ->orWhereHas('dichVu', fn ($serviceQuery) => $serviceQuery->where('ten_dich_vu', 'ilike', "%{$search}%"));
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function getStatusCountsForProvider(int $providerId): array
    {
        return DonDatLich::where('nha_cung_cap_id', $providerId)
            ->selectRaw("trang_thai_don, COUNT(*) as so_luong")
            ->groupBy('trang_thai_don')
            ->pluck('so_luong', 'trang_thai_don')
            ->toArray();
    }

    public function getRecentForCustomer(int $customerId, int $limit = 5): Collection
    {
        return DonDatLich::with(['danhGia', 'dichVu.danhMuc', 'dichVu.nhaCungCap.hoSoNhaCungCap', 'nhaCungCap.hoSoNhaCungCap'])
            ->where('khach_hang_id', $customerId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getUpcomingForCustomer(int $customerId, int $limit = 3): Collection
    {
        return DonDatLich::with(['danhGia', 'dichVu.danhMuc', 'dichVu.nhaCungCap.hoSoNhaCungCap', 'nhaCungCap.hoSoNhaCungCap'])
            ->where('khach_hang_id', $customerId)
            ->whereIn('trang_thai_don', ['cho_xac_nhan', 'da_xac_nhan', 'dang_thuc_hien'])
            ->whereNotNull('thoi_gian_thuc_hien')
            ->where('thoi_gian_thuc_hien', '>=', now())
            ->orderBy('thoi_gian_thuc_hien')
            ->limit($limit)
            ->get();
    }

    public function getAllForCustomer(int $customerId): Collection
    {
        return DonDatLich::with(['dichVu', 'nhaCungCap'])
            ->where('khach_hang_id', $customerId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function countForCustomer(int $customerId, ?string $status = null): int
    {
        $query = DonDatLich::where('khach_hang_id', $customerId);
        if ($status) {
            $query->where('trang_thai_don', $status);
        }
        return $query->count();
    }

    public function countUpcomingForCustomer(int $customerId): int
    {
        return DonDatLich::where('khach_hang_id', $customerId)
            ->whereIn('trang_thai_don', ['cho_xac_nhan', 'da_xac_nhan', 'dang_thuc_hien'])
            ->whereNotNull('thoi_gian_thuc_hien')
            ->where('thoi_gian_thuc_hien', '>=', now())
            ->count();
    }

    public function countReviewPendingForCustomer(int $customerId): int
    {
        return DonDatLich::where('khach_hang_id', $customerId)
            ->where('trang_thai_don', 'hoan_thanh')
            ->doesntHave('danhGia')
            ->count();
    }

    public function getPaginatedForAdmin(?string $status, ?string $search, int $perPage = 15): LengthAwarePaginator
    {
        $query = DonDatLich::with(['khachHang', 'nhaCungCap', 'dichVu']);

        if ($status && $status !== 'all') {
            $query->where('trang_thai_don', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_don', 'ilike', "%{$search}%")
                  ->orWhereHas('khachHang', fn ($q) => $q->where('ho_ten', 'ilike', "%{$search}%"))
                  ->orWhereHas('nhaCungCap', fn ($q) => $q->where('ho_ten', 'ilike', "%{$search}%"));
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getStatusCountsForAdmin(): array
    {
        return [
            'all' => DonDatLich::count(),
            'cho_xac_nhan' => DonDatLich::where('trang_thai_don', 'cho_xac_nhan')->count(),
            'da_xac_nhan' => DonDatLich::where('trang_thai_don', 'da_xac_nhan')->count(),
            'dang_thuc_hien' => DonDatLich::where('trang_thai_don', 'dang_thuc_hien')->count(),
            'hoan_thanh' => DonDatLich::where('trang_thai_don', 'hoan_thanh')->count(),
            'da_huy' => DonDatLich::where('trang_thai_don', 'da_huy')->count(),
        ];
    }

    public function create(array $data): DonDatLich
    {
        return DonDatLich::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $booking = DonDatLich::find($id);
        if (!$booking) return false;
        
        return $booking->update($data);
    }
}
