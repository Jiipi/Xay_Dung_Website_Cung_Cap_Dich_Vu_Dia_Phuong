<?php

namespace Tests\Feature\Category;

use App\Models\DanhMucDichVu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_catalog_page_can_be_rendered(): void
    {
        $tour = DanhMucDichVu::query()->create([
            'ten_danh_muc' => 'Du lịch',
            'slug' => 'du-lich',
            'mo_ta' => 'Danh mục dịch vụ du lịch',
            'thu_tu_hien_thi' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        DanhMucDichVu::query()->create([
            'parent_id' => $tour->id,
            'ten_danh_muc' => 'Tour săn mây',
            'slug' => 'tour-san-may',
            'mo_ta' => 'Tour khởi hành sáng sớm',
            'trang_thai' => 'hoat_dong',
        ]);

        $response = $this->get(route('categories.index'));

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('categories/Index')
            ->where('filters.status', 'hoat_dong')
            ->has('categories', 1)
            ->where('categories.0.ten_danh_muc', 'Du lịch')
            ->where('categories.0.tong_danh_muc_con', 1)
            ->where('categories.0.danh_muc_con.0.ten_danh_muc', 'Tour săn mây'));
    }

    public function test_category_catalog_page_can_filter_by_search_term(): void
    {
        DanhMucDichVu::query()->create([
            'ten_danh_muc' => 'Du lịch',
            'slug' => 'du-lich',
            'mo_ta' => 'Danh mục dịch vụ du lịch',
            'thu_tu_hien_thi' => 1,
            'trang_thai' => 'hoat_dong',
        ]);

        DanhMucDichVu::query()->create([
            'ten_danh_muc' => 'Sửa chữa',
            'slug' => 'sua-chua',
            'mo_ta' => 'Danh mục dịch vụ sửa chữa',
            'thu_tu_hien_thi' => 2,
            'trang_thai' => 'hoat_dong',
        ]);

        $response = $this->get(route('categories.index', ['search' => 'sua']));

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('categories/Index')
            ->where('filters.search', 'sua')
            ->has('categories', 1)
            ->where('categories.0.ten_danh_muc', 'Sửa chữa'));
    }
}
