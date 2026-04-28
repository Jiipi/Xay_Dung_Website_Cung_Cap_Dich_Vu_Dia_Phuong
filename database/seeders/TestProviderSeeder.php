<?php

namespace Database\Seeders;

use App\Models\HoSoNhaCungCap;
use App\Models\User;
use App\Models\VaiTroNguoiDung;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestProviderSeeder extends Seeder
{
    public function run(): void
    {
        $role = VaiTroNguoiDung::where('ten_vai_tro', 'Nhà cung cấp')->first();

        if (! $role) {
            $this->command->error('Role "Nhà cung cấp" not found. Run php artisan migrate:fresh --seed first.');
            return;
        }

        // Skip if already exists
        if (User::where('email', 'provider@test.com')->exists()) {
            $this->command->info('Account provider@test.com already exists.');
            return;
        }

        $user = User::create([
            'ho_ten' => 'NCC Test Provider',
            'email' => 'provider@test.com',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => $role->id,
            'trang_thai' => 'hoat_dong',
            'email_da_xac_minh' => now(),
        ]);

        HoSoNhaCungCap::create([
            'id' => $user->id,
            'ten_thuong_hieu' => 'Dịch Vụ Pro Đà Lạt',
            'gioi_thieu' => 'Nhà cung cấp dịch vụ chuyên nghiệp tại Đà Lạt',
            'nam_kinh_nghiem' => 5,
            'diem_danh_gia' => 4.80,
        ]);

        $this->command->info("Created: provider@test.com / password (ID: {$user->id})");
    }
}
