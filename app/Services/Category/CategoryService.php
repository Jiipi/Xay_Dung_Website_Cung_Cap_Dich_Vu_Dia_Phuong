<?php

namespace App\Services\Category;

use App\Data\DTOs\Category\CategoryIndexData;
use App\Models\DichVu;
use App\Repositories\Contracts\Category\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    /**
     * Get category data ready for the public category catalog page.
     */
    public function getCatalog(CategoryIndexData $filters): Collection
    {
        // Pre-load service counts per category
        $serviceCounts = DichVu::where('trang_thai_hoat_dong', 'hoat_dong')
            ->selectRaw('danh_muc_id, count(*) as total')
            ->groupBy('danh_muc_id')
            ->pluck('total', 'danh_muc_id');

        return $this->categories
            ->getListing($filters)
            ->map(function ($category) use ($serviceCounts) {
                // Đếm dịch vụ thuộc category cha + tất cả con
                $childIds = $category->children->pluck('id');
                $allIds = $childIds->push($category->id);
                $totalServices = $allIds->sum(fn ($id) => $serviceCounts[$id] ?? 0);

                return [
                    'id' => $category->id,
                    'ten_danh_muc' => $category->ten_danh_muc,
                    'slug' => $category->slug,
                    'mo_ta' => $category->mo_ta,
                    'thu_tu_hien_thi' => $category->thu_tu_hien_thi,
                    'trang_thai' => $category->trang_thai,
                    'so_dich_vu' => $totalServices,
                    'tong_danh_muc_con' => $category->children->count(),
                    'danh_muc_con' => $category->children->map(fn ($child) => [
                        'id' => $child->id,
                        'ten_danh_muc' => $child->ten_danh_muc,
                        'slug' => $child->slug,
                        'mo_ta' => $child->mo_ta,
                        'trang_thai' => $child->trang_thai,
                        'so_dich_vu' => $serviceCounts[$child->id] ?? 0,
                    ])->values()->all(),
                ];
            })
            ->values();
    }
}
