<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\DichVu;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderAvailabilityController extends Controller
{
    /**
     * Hiển thị lịch làm việc của provider.
     * Lấy từ field lich_lam_viec (JSON) của các dịch vụ, hoặc từ profile.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Cấu trúc mặc định: 7 ngày trong tuần
        $defaultSchedule = [
            ['thu' => 'Thứ 2', 'bat_dau' => '08:00', 'ket_thuc' => '17:00', 'hoat_dong' => true],
            ['thu' => 'Thứ 3', 'bat_dau' => '08:00', 'ket_thuc' => '17:00', 'hoat_dong' => true],
            ['thu' => 'Thứ 4', 'bat_dau' => '08:00', 'ket_thuc' => '17:00', 'hoat_dong' => true],
            ['thu' => 'Thứ 5', 'bat_dau' => '08:00', 'ket_thuc' => '17:00', 'hoat_dong' => true],
            ['thu' => 'Thứ 6', 'bat_dau' => '08:00', 'ket_thuc' => '17:00', 'hoat_dong' => true],
            ['thu' => 'Thứ 7', 'bat_dau' => '08:00', 'ket_thuc' => '12:00', 'hoat_dong' => true],
            ['thu' => 'Chủ nhật', 'bat_dau' => '', 'ket_thuc' => '', 'hoat_dong' => false],
        ];

        // Tìm dịch vụ đầu tiên có lịch làm việc, hoặc trả về mặc định
        $service = DichVu::where('nha_cung_cap_id', $user->id)
            ->whereNotNull('lich_lam_viec')
            ->first();

        $schedule = $service?->lich_lam_viec ?? $defaultSchedule;

        return Inertia::render('provider/Availability', [
            'schedule' => $schedule,
        ]);
    }

    /**
     * Cập nhật lịch làm việc cho tất cả dịch vụ của provider.
     */
    public function update(Request $request)
    {
        $request->validate([
            'schedule' => 'required|array|min:7|max:7',
            'schedule.*.thu' => 'required|string',
            'schedule.*.bat_dau' => 'nullable|string|max:5',
            'schedule.*.ket_thuc' => 'nullable|string|max:5',
            'schedule.*.hoat_dong' => 'required|boolean',
        ]);

        $user = $request->user();
        $schedule = $request->input('schedule');

        // Cập nhật lịch cho tất cả dịch vụ của provider
        DichVu::where('nha_cung_cap_id', $user->id)
            ->update(['lich_lam_viec' => json_encode($schedule)]);

        return back()->with('success', 'Lịch làm việc đã được cập nhật!');
    }
}
