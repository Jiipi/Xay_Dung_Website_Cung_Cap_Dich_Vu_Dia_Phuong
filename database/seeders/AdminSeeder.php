<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VaiTroNguoiDung;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = VaiTroNguoiDung::where('ten_vai_tro', 'Admin')->first();

        if (! $role) {
            $this->command->error('Role "Admin" not found. Run php artisan migrate:fresh --seed first.');
            return;
        }

        if (User::where('email', 'admin@dalat.com')->exists()) {
            $this->command->info('Account admin@dalat.com already exists.');
            return;
        }

        User::create([
            'ho_ten' => 'Admin Dalat',
            'email' => 'admin@dalat.com',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => $role->id,
            'trang_thai' => 'hoat_dong',
            'email_da_xac_minh' => now(),
        ]);

        $this->command->info('Created: admin@dalat.com / password');
    }
}
