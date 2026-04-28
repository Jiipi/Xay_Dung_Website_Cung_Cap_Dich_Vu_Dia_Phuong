<?php

namespace Database\Seeders;

use App\Models\DanhGia;
use App\Models\DichVu;
use App\Models\DonDatLich;
use App\Models\User;
use App\Models\VaiTroNguoiDung;
use Illuminate\Database\Seeder;

class GiaoDichSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleCustomer = VaiTroNguoiDung::where('ten_vai_tro', 'Khách hàng')->first();
        $customers = User::where('vai_tro', $roleCustomer->id)->get();
        $services = DichVu::all();

        if ($customers->isEmpty() || $services->isEmpty()) {
            return;
        }

        // Tạo 100 giao dịch (Đơn đặt lịch) ngẫu nhiên
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $service = $services->random();

            $booking = DonDatLich::factory()->create([
                'khach_hang_id' => $customer->id,
                'nha_cung_cap_id' => $service->nha_cung_cap_id,
                'dich_vu_id' => $service->id,
            ]);

            // Nếu đơn ở trạng thái hoàn thành, tỷ lệ 70% khách sẽ để lại đánh giá
            if ($booking->trang_thai_don === 'hoan_thanh' && fake()->boolean(70)) {
                DanhGia::factory()->create([
                    'don_dat_lich_id' => $booking->id,
                    'nha_cung_cap_id' => $booking->nha_cung_cap_id,
                    'khach_hang_id' => $booking->khach_hang_id,
                ]);
            }
        }
    }
}
