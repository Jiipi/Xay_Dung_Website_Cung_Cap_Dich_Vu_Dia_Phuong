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

        return [
            'ma_don' => strtoupper(Str::random(10)),
            'khach_hang_id' => null, // Assigned in Seeder
            'nha_cung_cap_id' => null, // Assigned in Seeder
            'dich_vu_id' => null, // Assigned in Seeder
            'thoi_gian_thuc_hien' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'so_luong' => $soLuong,
            'don_vi' => 'Lượt',
            'dia_diem_thuc_hien' => fake()->address(),
            'ghi_chu' => fake()->boolean(50) ? fake()->sentence() : null,
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
