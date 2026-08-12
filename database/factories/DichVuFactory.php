<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DichVu>
 */
class DichVuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $samples = [
            ['name' => 'Dọn dẹp nhà cửa theo giờ', 'desc' => 'Nhân viên hỗ trợ lau dọn, sắp xếp đồ đạc và vệ sinh khu vực bếp, phòng ngủ, nhà tắm cho gia đình bận rộn.', 'keywords' => ['dọn nhà', 'vệ sinh', 'giúp việc'], 'image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Sửa điện nước tại nhà', 'desc' => 'Thợ kiểm tra và xử lý rò rỉ nước, tắc nghẽn, chập điện, thay ổ cắm hoặc thiết bị dân dụng với báo giá rõ ràng.', 'keywords' => ['sửa điện', 'sửa nước', 'thợ tận nơi'], 'image' => 'https://images.unsplash.com/photo-1607472586893-edb57bdc0e39?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Vệ sinh máy lạnh định kỳ', 'desc' => 'Làm sạch lưới lọc, dàn lạnh, kiểm tra gas và vận hành thử để máy lạnh hoạt động ổn định, ít hao điện.', 'keywords' => ['máy lạnh', 'điều hòa', 'bảo trì'], 'image' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Thuê xe máy giao tận nơi', 'desc' => 'Cho thuê xe số, xe ga theo ngày, kèm mũ bảo hiểm và hỗ trợ giao nhận tại khách sạn, homestay hoặc nhà riêng.', 'keywords' => ['thuê xe máy', 'du lịch', 'giao xe'], 'image' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1200&q=80'],
            ['name' => 'Trang điểm dự tiệc tại nhà', 'desc' => 'Makeup tự nhiên, làm tóc nhẹ và chuẩn bị diện mạo phù hợp cho tiệc cưới, sinh nhật, chụp ảnh hoặc sự kiện công ty.', 'keywords' => ['trang điểm', 'makeup', 'làm đẹp'], 'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1200&q=80'],
        ];
        $sample = fake()->randomElement($samples);
        $tenDichVu = $sample['name'];
        $giaMin = fake()->numberBetween(9, 80) * 10000;
        $giaMax = $giaMin + fake()->numberBetween(10, 150) * 10000;

        return [
            'nha_cung_cap_id' => null, // Seeder sẽ gán nhà cung cấp cụ thể.
            'danh_muc_id' => null,     // Seeder sẽ gán danh mục con phù hợp.
            'ten_dich_vu' => $tenDichVu,
            'slug' => Str::slug($tenDichVu) . '-' . Str::random(5),
            'mo_ta_chi_tiet' => $sample['desc'],
            'gia_tu' => $giaMin,
            'gia_den' => fake()->boolean(70) ? $giaMax : null, // Thỉnh thoảng chỉ hiển thị một mức giá cố định.
            'don_vi_gia' => fake()->randomElement(['Lượt', 'Giờ', 'Gói', 'Ngày']),
            'dia_chi_hien_thi' => fake()->randomElement(['Quận 1, TP. Hồ Chí Minh', 'Cầu Giấy, Hà Nội', 'Hải Châu, Đà Nẵng', 'Trung tâm Đà Lạt']),
            'tinh_thanh_id' => null,
            'quan_huyen_id' => null,
            'toa_do_lat' => fake()->latitude(11.8, 12.0), // Gần Đà Lạt
            'toa_do_lng' => fake()->longitude(108.3, 108.5), // Gần Đà Lạt
            'danh_sach_anh' => [
                $sample['image'],
            ],
            'the_tu_khoa' => $sample['keywords'],
            'khu_vuc_phuc_vu' => ['Phường 1', 'Phường 2', 'Trung tâm Đà Lạt'],
            'thuoc_tinh' => ['Bảo hành' => '1 tháng', 'Xuất hóa đơn' => 'Có'],
            'lich_lam_viec' => [
                'T2-T6' => '08:00 - 18:00',
                'T7-CN' => '09:00 - 12:00'
            ],
            'do_uu_tien' => fake()->boolean(20) ? fake()->numberBetween(1, 10) : 0,
            'trang_thai_duyet' => 'da_duyet',
            'trang_thai_hoat_dong' => 'hoat_dong',
        ];
    }
}
