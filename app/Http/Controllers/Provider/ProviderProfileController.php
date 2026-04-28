<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\HoSoNhaCungCap;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderProfileController extends Controller
{
    /**
     * Hiển thị form sửa hồ sơ nhà cung cấp.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->hoSoNhaCungCap;

        return Inertia::render('provider/Profile', [
            'profile' => $profile ? [
                'ten_thuong_hieu' => $profile->ten_thuong_hieu,
                'gioi_thieu' => $profile->gioi_thieu,
                'nam_kinh_nghiem' => $profile->nam_kinh_nghiem,
                'website' => $profile->website,
                'facebook' => $profile->facebook,
                'giay_phep_kinh_doanh' => $profile->giay_phep_kinh_doanh,
                'stk_ngan_hang' => $profile->stk_ngan_hang,
                'ten_ngan_hang' => $profile->ten_ngan_hang,
                'ten_chu_tk' => $profile->ten_chu_tk,
                'diem_danh_gia' => (float) $profile->diem_danh_gia,
            ] : null,
            'user' => [
                'ho_ten' => $user->ho_ten,
                'email' => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'anh_dai_dien' => $user->anh_dai_dien,
            ],
        ]);
    }

    /**
     * Cập nhật hồ sơ nhà cung cấp.
     */
    public function update(Request $request)
    {
        $request->validate([
            'ten_thuong_hieu' => 'required|string|max:120',
            'gioi_thieu' => 'nullable|string|max:2000',
            'nam_kinh_nghiem' => 'nullable|integer|min:0|max:100',
            'website' => 'nullable|url|max:2048',
            'facebook' => 'nullable|string|max:2048',
            'giay_phep_kinh_doanh' => 'nullable|string|max:2048',
            'stk_ngan_hang' => 'nullable|string|max:20',
            'ten_ngan_hang' => 'nullable|string|max:50',
            'ten_chu_tk' => 'nullable|string|max:50',
            // User fields
            'ho_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
        ]);

        $user = $request->user();

        // Cập nhật thông tin user
        $user->update([
            'ho_ten' => $request->input('ho_ten'),
            'so_dien_thoai' => $request->input('so_dien_thoai'),
        ]);

        // Xử lý upload avatar
        if ($request->hasFile('anh_dai_dien')) {
            $request->validate(['anh_dai_dien' => 'image|mimes:jpg,jpeg,png,webp|max:2048']);
            $path = $request->file('anh_dai_dien')->store('avatars/' . $user->id, 'public');
            $user->update(['anh_dai_dien' => '/storage/' . $path]);
        }

        // Cập nhật hồ sơ nhà cung cấp
        $profile = $user->hoSoNhaCungCap;
        if ($profile) {
            $profile->update([
                'ten_thuong_hieu' => $request->input('ten_thuong_hieu'),
                'gioi_thieu' => $request->input('gioi_thieu'),
                'nam_kinh_nghiem' => $request->input('nam_kinh_nghiem', 0),
                'website' => $request->input('website'),
                'facebook' => $request->input('facebook'),
                'giay_phep_kinh_doanh' => $request->input('giay_phep_kinh_doanh'),
                'stk_ngan_hang' => $request->input('stk_ngan_hang'),
                'ten_ngan_hang' => $request->input('ten_ngan_hang'),
                'ten_chu_tk' => $request->input('ten_chu_tk'),
            ]);
        } else {
            HoSoNhaCungCap::create([
                'id' => $user->id,
                'ten_thuong_hieu' => $request->input('ten_thuong_hieu'),
                'gioi_thieu' => $request->input('gioi_thieu'),
                'nam_kinh_nghiem' => $request->input('nam_kinh_nghiem', 0),
                'website' => $request->input('website'),
                'facebook' => $request->input('facebook'),
                'giay_phep_kinh_doanh' => $request->input('giay_phep_kinh_doanh'),
                'stk_ngan_hang' => $request->input('stk_ngan_hang'),
                'ten_ngan_hang' => $request->input('ten_ngan_hang'),
                'ten_chu_tk' => $request->input('ten_chu_tk'),
            ]);
        }

        return back()->with('success', 'Hồ sơ đã được cập nhật thành công!');
    }
}
