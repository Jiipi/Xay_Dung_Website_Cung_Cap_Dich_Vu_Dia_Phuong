<?php

namespace App\Repositories\Eloquent\Category;

use App\Data\DTOs\Category\CategoryIndexData;
use App\Models\DanhMucDichVu;
use App\Repositories\Contracts\Category\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Get marketplace categories for the public listing page.
     */
    public function getListing(CategoryIndexData $filters): Collection
    {
        return DanhMucDichVu::query()
            ->whereNull('parent_id')
            ->when($filters->status !== 'all', function (Builder $query) use ($filters) {
                $query->where('trang_thai', $filters->status);
            })
            ->when($filters->search, function (Builder $query) use ($filters) {
                $search = $filters->search;

                $query->where(function (Builder $innerQuery) use ($search) {
                    $innerQuery
                        ->where('ten_danh_muc', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('mo_ta', 'like', "%{$search}%")
                        ->orWhereHas('children', function (Builder $subQuery) use ($search) {
                            $subQuery
                                ->where('ten_danh_muc', 'like', "%{$search}%")
                                ->orWhere('slug', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters->withSubcategories, function (Builder $query) {
                $query->with([
                    'children' => fn ($subQuery) => $subQuery
                        ->orderBy('ten_danh_muc')
                        ->select([
                            'id',
                            'parent_id',
                            'ten_danh_muc',
                            'slug',
                            'mo_ta',
                            'trang_thai',
                        ]),
                ]);
            })
            ->orderBy('thu_tu_hien_thi')
            ->orderBy('ten_danh_muc')
            ->get();
    }
}
