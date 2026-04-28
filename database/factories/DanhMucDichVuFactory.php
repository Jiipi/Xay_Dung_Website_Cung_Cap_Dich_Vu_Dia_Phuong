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
        $name = fake()->unique()->words(2, true);
        
        return [
            'ten_danh_muc' => ucfirst($name),
            'slug' => Str::slug($name),
            'mo_ta' => fake()->sentence(),
            'anh_dai_dien' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random',
            'thu_tu_hien_thi' => fake()->numberBetween(0, 100),
            'trang_thai' => fake()->randomElement(['hoat_dong', 'hoat_dong', 'ngung_hoat_dong']),
            'parent_id' => null, // Sẽ được setup trong seeder nếu muốn làm phân cấp
        ];
    }
}
