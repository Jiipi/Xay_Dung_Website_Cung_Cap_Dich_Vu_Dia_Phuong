<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DanhGia>
 */
class DanhGiaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasReply = fake()->boolean(40);
        $reviews = [
            'Thợ đến đúng giờ, làm việc gọn gàng và giải thích rõ chi phí trước khi bắt đầu. Tôi sẽ đặt lại khi cần.',
            'Dịch vụ ổn, nhân viên thân thiện, khu vực sau khi làm sạch hơn nhiều so với mong đợi.',
            'Đặt lịch khá nhanh, nhà cung cấp phản hồi sớm và hỗ trợ đổi giờ linh hoạt cho gia đình tôi.',
            'Giá hợp lý, thái độ chuyên nghiệp. Phần tư vấn ban đầu rất dễ hiểu và không phát sinh lặt vặt.',
            'Trải nghiệm tốt, phù hợp cho người bận rộn cần dịch vụ tận nơi trong ngày.',
            'Có mặt hơi trễ vài phút nhưng báo trước, chất lượng hoàn thành tốt nên vẫn hài lòng.',
        ];
        $replies = [
            'Cảm ơn anh/chị đã tin tưởng, bên em rất vui được hỗ trợ trong những lần tiếp theo.',
            'Cảm ơn góp ý của anh/chị, bên em sẽ tiếp tục cải thiện để phục vụ tốt hơn.',
            'Rất cảm ơn đánh giá của anh/chị. Nếu cần hỗ trợ thêm, anh/chị cứ nhắn cho bên em nhé.',
        ];
        
        return [
            'don_dat_lich_id' => null, // Seeder sẽ gán đơn đặt lịch cụ thể.
            'nha_cung_cap_id' => null, // Seeder sẽ gán nhà cung cấp tương ứng.
            'khach_hang_id' => null, // Seeder sẽ gán khách hàng đã đặt dịch vụ.
            'so_sao' => fake()->numberBetween(3, 5), // Đánh giá thường khá cao để dữ liệu demo đẹp.
            'noi_dung' => fake()->randomElement($reviews),
            'an_danh' => fake()->boolean(20),
            'phan_hoi_tu_ncc' => $hasReply ? fake()->randomElement($replies) : null,
            'ngay_phan_hoi' => $hasReply ? fake()->dateTimeBetween('-1 week', 'now') : null,
        ];
    }
}
