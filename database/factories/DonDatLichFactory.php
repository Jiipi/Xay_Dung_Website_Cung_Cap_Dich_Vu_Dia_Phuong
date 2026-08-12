<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonDatLich>
 */
class DonDatLichFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $soLuong = fake()->numberBetween(1, 3);
        $giaTemp = fake()->numberBetween(5, 50) * 100000;
        $tamTinh = $giaTemp * $soLuong;
        $phiDichVu = $tamTinh * 0.05;
        $giamGia = fake()->boolean(30) ? $tamTinh * 0.1 : 0;
        $tongTien = $tamTinh + $phiDichVu - $giamGia;
        $addresses = [
            '12 Nguyễn Văn Cừ, phường 1, Đà Lạt, Lâm Đồng',
            '45 Hai Bà Trưng, phường 6, Đà Lạt, Lâm Đồng',
            '88 Phan Đình Phùng, phường 2, Đà Lạt, Lâm Đồng',
            '23 Trần Phú, phường 3, Đà Lạt, Lâm Đồng',
            '156 Bùi Thị Xuân, phường 8, Đà Lạt, Lâm Đồng',
        ];
        $notes = [
            'Vui lòng gọi trước 15 phút khi đến nơi.',
            'Nhà có chỗ gửi xe, cần mang đủ dụng cụ hỗ trợ.',
            'Ưu tiên khung giờ buổi sáng vì gia đình có người ở nhà.',
            'Cần tư vấn thêm nếu phát sinh hạng mục ngoài mô tả.',
        ];

        return [
            'ma_don' => strtoupper(Str::random(10)),
            'khach_hang_id' => null, // Seeder sẽ gán khách hàng cụ thể.
            'nha_cung_cap_id' => null, // Seeder sẽ gán nhà cung cấp cụ thể.
            'dich_vu_id' => null, // Seeder sẽ gán dịch vụ cụ thể.
            'thoi_gian_thuc_hien' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'so_luong' => $soLuong,
            'don_vi' => 'Lượt',
            'dia_diem_thuc_hien' => fake()->randomElement($addresses),
            'ghi_chu' => fake()->boolean(50) ? fake()->randomElement($notes) : null,
            'ma_khuyen_mai' => $giamGia > 0 ? strtoupper(Str::random(5)) : null,
            'tam_tinh' => $tamTinh,
            'phi_dich_vu' => $phiDichVu,
            'giam_gia' => $giamGia,
            'tong_tien' => $tongTien,
            'trang_thai_don' => fake()->randomElement(['cho_xac_nhan', 'da_xac_nhan', 'dang_thuc_hien', 'hoan_thanh', 'da_huy']),
            'phuong_thuc_thanh_toan' => fake()->randomElement(['tien_mat', 'chuyen_khoan', 'vnpay']),
            'trang_thai_thanh_toan' => fake()->randomElement(['cho_thanh_toan', 'da_thanh_toan', 'hoan_tien']),
            'ma_giao_dich_doi_tac' => fake()->boolean(50) ? strtoupper(Str::random(15)) : null,
        ];
    }
}
