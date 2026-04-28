<?php

namespace Database\Seeders;

use App\Models\DanhMucDichVu;
use App\Models\DichVu;
use App\Models\HoSoNhaCungCap;
use Illuminate\Database\Seeder;

class DichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = HoSoNhaCungCap::all();
        // Lấy danh sách các danh mục con (có parent_id != null)
        $subCategories = DanhMucDichVu::whereNotNull('parent_id')->get();

        if ($providers->isEmpty() || $subCategories->isEmpty()) {
            return;
        }

        // Tạo 100 dịch vụ mẫu gắn ngẫu nhiên cho Provider và Categories
        foreach ($providers as $provider) {
            // Mỗi nhà cung cấp có từ 1 đến 5 dịch vụ khác nhau
            $soDichVuTrongKho = rand(1, 5);
            
            for ($i = 0; $i < $soDichVuTrongKho; $i++) {
                DichVu::factory()->create([
                    'nha_cung_cap_id' => $provider->id,
                    'danh_muc_id' => $subCategories->random()->id,
                ]);
            }
        }
    }
}
