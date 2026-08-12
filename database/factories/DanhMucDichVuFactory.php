<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DanhMucDichVu>
 */
class DanhMucDichVuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Dọn dẹp nhà cửa', 'Sửa điện nước', 'Vệ sinh máy lạnh', 'Chăm sóc thú cưng',
            'Trang điểm tại nhà', 'Gia sư học sinh', 'Thuê xe máy', 'Chụp ảnh sự kiện',
        ];
        $name = fake()->unique()->randomElement($categories);
        $descriptions = [
            'Nhóm dịch vụ phổ biến, phù hợp nhu cầu sinh hoạt hằng ngày tại Việt Nam.',
            'Dịch vụ tận nơi với lịch hẹn linh hoạt, báo giá rõ ràng trước khi thực hiện.',
            'Các lựa chọn hỗ trợ gia đình, căn hộ, văn phòng nhỏ và homestay.',
        ];
        
        return [
            'ten_danh_muc' => $name,
            'slug' => Str::slug($name),
            'mo_ta' => fake()->randomElement($descriptions),
            'anh_dai_dien' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random',
            'thu_tu_hien_thi' => fake()->numberBetween(0, 100),
            'trang_thai' => fake()->randomElement(['hoat_dong', 'hoat_dong', 'ngung_hoat_dong']),
            'parent_id' => null, // Sẽ được setup trong seeder nếu muốn làm phân cấp
        ];
    }
}
