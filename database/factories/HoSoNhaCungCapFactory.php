<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HoSoNhaCungCap>
 */
class HoSoNhaCungCapFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $providers = [
            ['brand' => 'Nhà Sạch An Tâm', 'intro' => 'Đội ngũ chuyên dọn dẹp căn hộ, nhà phố và homestay theo giờ. Nhân viên đúng hẹn, dụng cụ sạch và báo giá rõ trước khi làm.'],
            ['brand' => 'Điện Nước Minh Phát', 'intro' => 'Nhận sửa điện nước gia đình, thay thiết bị dân dụng và xử lý sự cố khẩn cấp trong nội thành. Thợ có kinh nghiệm, làm gọn và có bảo hành.'],
            ['brand' => 'Máy Lạnh Mát Lành', 'intro' => 'Chuyên vệ sinh, bảo trì máy lạnh treo tường cho hộ gia đình, văn phòng nhỏ và homestay. Quy trình sạch sẽ, kiểm tra máy sau khi hoàn tất.'],
            ['brand' => 'Xe Máy Du Lịch Đà Lạt', 'intro' => 'Cho thuê xe máy giao tận nơi, xe được bảo dưỡng định kỳ, kèm mũ bảo hiểm và hỗ trợ khách trong suốt thời gian thuê.'],
            ['brand' => 'Trang Điểm Mộc Studio', 'intro' => 'Nhận trang điểm dự tiệc, chụp ảnh và làm tóc nhẹ tại nhà. Phong cách tự nhiên, phù hợp tiệc cưới, sinh nhật và sự kiện công ty.'],
            ['brand' => 'Thú Y Tận Nhà Sen Vàng', 'intro' => 'Khám sức khỏe chó mèo tại nhà, tư vấn tiêm phòng và chăm sóc thú cưng sau điều trị. Có hỗ trợ đặt lịch ngoài giờ.'],
        ];
        $provider = fake()->randomElement($providers);
        $accountNames = ['Nguyễn Văn An', 'Trần Thị Bình', 'Lê Minh Châu', 'Phạm Quốc Dũng', 'Võ Ngọc Hà'];

        return [
            'ten_thuong_hieu' => $provider['brand'],
            'gioi_thieu' => $provider['intro'],
            'nam_kinh_nghiem' => fake()->numberBetween(1, 20),
            'website' => fake()->url(),
            'facebook' => 'https://facebook.com/' . fake()->userName(),
            'giay_phep_kinh_doanh' => fake()->ean13(),
            'stk_ngan_hang' => fake()->numerify('000##########'),
            'ten_ngan_hang' => fake()->randomElement(['Vietcombank', 'Techcombank', 'MB Bank', 'ACB', 'BIDV']),
            'ten_chu_tk' => fake()->randomElement($accountNames),
            'ty_le_hoa_hong' => fake()->randomFloat(2, 5, 20),
            'diem_danh_gia' => fake()->randomFloat(2, 3.5, 5),
        ];
    }
}
