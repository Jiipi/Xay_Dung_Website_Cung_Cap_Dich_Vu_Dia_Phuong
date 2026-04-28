<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VaiTroNguoiDung;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DanhMucDichVuSeeder::class,
            NguoiDungSeeder::class,
            DichVuSeeder::class,
            GiaoDichSeeder::class,
        ]);
    }
}
