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
        
        return [
            'don_dat_lich_id' => null, // Assigned in Seeder
            'nha_cung_cap_id' => null, // Assigned in Seeder
            'khach_hang_id' => null, // Assigned in Seeder
            'so_sao' => fake()->numberBetween(3, 5), // Reviews thường khá cao để demo đẹp
            'noi_dung' => fake()->paragraph(),
            'an_danh' => fake()->boolean(20),
            'phan_hoi_tu_ncc' => $hasReply ? fake()->sentence() : null,
            'ngay_phan_hoi' => $hasReply ? fake()->dateTimeBetween('-1 week', 'now') : null,
        ];
    }
}
