<?php

namespace App\Repositories\Eloquent\Review;

use App\Models\DanhGia;
use App\Repositories\Contracts\Review\ReviewRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentReviewRepository implements ReviewRepositoryInterface
{
    public function findById(int $id): ?DanhGia
    {
        return DanhGia::find($id);
    }

    public function findByIdAndProvider(int $id, int $providerId): ?DanhGia
    {
        return DanhGia::where('nha_cung_cap_id', $providerId)->find($id);
    }

    public function getPaginatedForProvider(int $providerId, ?int $stars, ?bool $unreplied, int $perPage = 10): LengthAwarePaginator
    {
        $query = DanhGia::with(['khachHang', 'donDatLich.dichVu'])
            ->where('nha_cung_cap_id', $providerId);

        if ($stars) {
            $query->where('so_sao', $stars);
        }

        if ($unreplied) {
            $query->whereNull('phan_hoi_tu_ncc');
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function getStatsForProvider(int $providerId): array
    {
        return [
            'tong' => DanhGia::where('nha_cung_cap_id', $providerId)->count(),
            'trung_binh' => round(DanhGia::where('nha_cung_cap_id', $providerId)->avg('so_sao') ?? 0, 1),
            'chua_phan_hoi' => DanhGia::where('nha_cung_cap_id', $providerId)->whereNull('phan_hoi_tu_ncc')->count(),
            'phan_bo' => DanhGia::where('nha_cung_cap_id', $providerId)
                ->selectRaw('so_sao, COUNT(*) as so_luong')
                ->groupBy('so_sao')
                ->pluck('so_luong', 'so_sao')
                ->toArray(),
        ];
    }

    public function getPaginatedForAdmin(?int $stars, ?string $search, int $perPage = 15): LengthAwarePaginator
    {
        $query = DanhGia::with(['khachHang', 'nhaCungCap', 'donDatLich.dichVu']);

        if ($stars) {
            $query->where('so_sao', $stars);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('noi_dung', 'ilike', "%{$search}%")
                  ->orWhereHas('khachHang', fn ($q) => $q->where('ho_ten', 'ilike', "%{$search}%"))
                  ->orWhereHas('nhaCungCap', fn ($q) => $q->where('ho_ten', 'ilike', "%{$search}%"));
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getStatsForAdmin(): array
    {
        return [
            'total' => DanhGia::count(),
            'avg_rating' => round(DanhGia::avg('so_sao') ?? 0, 1),
            'star_counts' => DanhGia::selectRaw('so_sao, count(*) as count')
                                ->groupBy('so_sao')
                                ->pluck('count', 'so_sao')
                                ->toArray(),
        ];
    }

    public function existsForBooking(int $bookingId): bool
    {
        return DanhGia::where('don_dat_lich_id', $bookingId)->exists();
    }

    public function create(array $data): DanhGia
    {
        return DanhGia::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $review = DanhGia::find($id);
        if (!$review) return false;
        
        return $review->update($data);
    }

    public function delete(int $id): bool
    {
        $review = DanhGia::find($id);
        if (!$review) return false;
        
        return $review->delete();
    }

    public function calculateAverageRatingForProvider(int $providerId): float
    {
        return round(DanhGia::where('nha_cung_cap_id', $providerId)->avg('so_sao') ?? 0, 2);
    }
}
