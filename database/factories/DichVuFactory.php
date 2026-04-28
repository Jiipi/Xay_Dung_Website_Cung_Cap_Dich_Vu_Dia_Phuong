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
        $tenDichVu = fake()->words(3, true);
        $giaMin = fake()->numberBetween(5, 50) * 10000;
        $giaMax = $giaMin + fake()->numberBetween(10, 100) * 10000;

        return [
            'nha_cung_cap_id' => null, // Assigned in Seeder
            'danh_muc_id' => null,     // Assigned in Seeder
            'ten_dich_vu' => ucfirst($tenDichVu),
            'slug' => Str::slug($tenDichVu) . '-' . Str::random(5),
            'mo_ta_chi_tiet' => fake()->paragraphs(3, true),
            'gia_tu' => $giaMin,
            'gia_den' => fake()->boolean(70) ? $giaMax : null, // Thỉnh thoảng không có giá tới (giá cố định)
            'don_vi_gia' => fake()->randomElement(['Lượt', 'Giờ', 'Gói']),
            'dia_chi_hien_thi' => fake()->address(),
            'tinh_thanh_id' => null,
            'quan_huyen_id' => null,
            'toa_do_lat' => fake()->latitude(11.8, 12.0), // Gần Đà Lạt
            'toa_do_lng' => fake()->longitude(108.3, 108.5), // Gần Đà Lạt
            'danh_sach_anh' => [
                'https://picsum.photos/seed/'.fake()->md5.'/800/600',
                'https://picsum.photos/seed/'.fake()->md5.'/800/600',
            ],
            'the_tu_khoa' => fake()->words(5),
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
