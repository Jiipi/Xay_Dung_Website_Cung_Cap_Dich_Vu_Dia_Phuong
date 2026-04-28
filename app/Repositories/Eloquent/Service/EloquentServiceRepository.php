<?php

namespace App\Repositories\Eloquent\Service;

use App\Models\DichVu;
use App\Repositories\Contracts\Service\ServiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentServiceRepository implements ServiceRepositoryInterface
{
    public function findById(int $id): ?DichVu
    {
        return DichVu::find($id);
    }

    public function findBySlug(string $slug): ?DichVu
    {
        return DichVu::where('slug', $slug)->first();
    }

    public function findByIdAndProvider(int $id, int $providerId): ?DichVu
    {
        return DichVu::where('nha_cung_cap_id', $providerId)->find($id);
    }

    public function getPaginatedForAdmin(?string $status, ?string $search, int $perPage = 15): LengthAwarePaginator
    {
        $query = DichVu::with(['nhaCungCap', 'danhMuc']);

        if ($status && $status !== 'all') {
            $query->where('trang_thai_duyet', $status);
        }

        if ($search) {
            $query->where('ten_dich_vu', 'ilike', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function getStatusCountsForAdmin(): array
    {
        return [
            'all' => DichVu::count(),
            'cho_duyet' => DichVu::where('trang_thai_duyet', 'cho_duyet')->count(),
            'da_duyet' => DichVu::where('trang_thai_duyet', 'da_duyet')->count(),
            'tu_choi' => DichVu::where('trang_thai_duyet', 'tu_choi')->count(),
        ];
    }

    public function getPaginatedForProvider(int $providerId, ?string $status, ?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $query = DichVu::where('nha_cung_cap_id', $providerId)->withCount('donDatLich');

        if ($search) {
            $query->where('ten_dich_vu', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('trang_thai_hoat_dong', $status);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function getActivePublicServices(): Collection
    {
        return DichVu::with(['nhaCungCap.hoSoNhaCungCap', 'danhMuc.parent'])
            ->where('trang_thai_hoat_dong', 'hoat_dong')
            ->orderByDesc('id')
            ->get();
    }

    public function create(array $data): DichVu
    {
        return DichVu::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $service = DichVu::find($id);
        if (!$service) return false;
        
        return $service->update($data);
    }

    public function delete(int $id): bool
    {
        $service = DichVu::find($id);
        if (!$service) return false;
        
        return $service->delete();
    }

    public function isSlugUniqueForProvider(string $slug, int $providerId, ?int $exceptId = null): bool
    {
        $query = DichVu::where('nha_cung_cap_id', $providerId)->where('slug', $slug);
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        return !$query->exists();
    }
}
