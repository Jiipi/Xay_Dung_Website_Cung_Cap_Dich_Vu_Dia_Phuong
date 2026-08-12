<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CauHinh;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = CauHinh::all()->pluck('value', 'key')->toArray();

        return Inertia::render('admin/Settings', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'platform_fee_percent' => 'required|numeric|min:0|max:100',
        ]);

        CauHinh::updateOrCreate(
            ['key' => 'platform_fee_percent'],
            ['value' => $request->platform_fee_percent, 'description' => 'Phí nền tảng (%) thu trên mỗi đơn hàng']
        );

        return back()->with('success', 'Cập nhật cấu hình thành công.');
    }
}
