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
        return [
            'ten_thuong_hieu' => fake()->company(),
            'gioi_thieu' => fake()->paragraphs(2, true),
            'nam_kinh_nghiem' => fake()->numberBetween(1, 20),
            'website' => fake()->url(),
            'facebook' => 'https://facebook.com/' . fake()->userName(),
            'giay_phep_kinh_doanh' => fake()->ean13(),
            'stk_ngan_hang' => fake()->numerify('000##########'),
            'ten_ngan_hang' => fake()->randomElement(['Vietcombank', 'Techcombank', 'MB Bank', 'ACB', 'BIDV']),
            'ten_chu_tk' => fake()->name(),
            'ty_le_hoa_hong' => fake()->randomFloat(2, 5, 20),
            'diem_danh_gia' => fake()->randomFloat(2, 3.5, 5),
        ];
    }
}
