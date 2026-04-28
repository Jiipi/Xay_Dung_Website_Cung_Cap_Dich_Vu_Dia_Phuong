<?php

namespace Database\Seeders;

use App\Models\DanhMucDichVu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DanhMucDichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Dọn dẹp & Vệ sinh' => ['Dọn nhà theo giờ', 'Vệ sinh máy lạnh', 'Giặt sofa nệm', 'Diệt côn trùng'],
            'Xây dựng & Sửa chữa' => ['Sửa điện nước', 'Sơn nhà', 'Chống thấm', 'Thợ mộc'],
            'Du lịch & Di chuyển' => ['Thuê xe máy', 'Thuê ô tô', 'Hướng dẫn viên', 'Đưa đón sân bay'],
            'Làm đẹp & Sức khỏe' => ['Spa tại nhà', 'Cắt tóc', 'Trang điểm tiệc', 'Massage', 'Bác sĩ thú y'],
            'Sự kiện & Giải trí' => ['Chụp ảnh', 'Trang trí tiệc', 'MC Sự kiện', 'Thuê lều trại'],
        ];

        $thuTu = 1;
        foreach ($categories as $parent => $children) {
            $parentCat = DanhMucDichVu::create([
                'ten_danh_muc' => $parent,
                'slug' => Str::slug($parent),
                'mo_ta' => "Các dịch vụ thuộc lĩnh vực $parent",
                'thu_tu_hien_thi' => $thuTu++,
            ]);

            $childThuTu = 1;
            foreach ($children as $child) {
                DanhMucDichVu::create([
                    'ten_danh_muc' => $child,
                    'slug' => Str::slug($child),
                    'parent_id' => $parentCat->id,
                    'thu_tu_hien_thi' => $childThuTu++,
                ]);
            }
        }
    }
}
