<?php

namespace Database\Seeders;

use App\Models\HoSoNhaCungCap;
use App\Models\User;
use App\Models\VaiTroNguoiDung;
use Illuminate\Database\Seeder;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo Role
        $roleAdmin = VaiTroNguoiDung::firstOrCreate(['ten_vai_tro' => 'Admin', 'mo_ta' => 'Quản trị viên']);
        $roleProvider = VaiTroNguoiDung::firstOrCreate(['ten_vai_tro' => 'Nhà cung cấp', 'mo_ta' => 'Người cung cấp dịch vụ']);
        $roleCustomer = VaiTroNguoiDung::firstOrCreate(['ten_vai_tro' => 'Khách hàng', 'mo_ta' => 'Người dùng tìm kiếm và đặt dịch vụ']);

        // Tạo 1 Admin
        User::factory()->create([
            'ho_ten' => 'Admin Chăm Sóc',
            'email' => 'admin@example.com',
            'vai_tro' => $roleAdmin->id,
        ]);

        // Tạo 1 Customer Cố định để test
        User::factory()->create([
            'ho_ten' => 'Khách Hàng Test',
            'email' => 'khach@example.com',
            'vai_tro' => $roleCustomer->id,
        ]);

        // Tạo 30 Khách hàng ảo
        User::factory(30)->create([
            'vai_tro' => $roleCustomer->id,
        ]);

        // Tạo 20 Nhà cung cấp ảo + Profile của họ
        User::factory(20)->create([
            'vai_tro' => $roleProvider->id,
        ])->each(function (User $user) {
            HoSoNhaCungCap::factory()->create([
                'id' => $user->id,
            ]);
        });
    }
}
