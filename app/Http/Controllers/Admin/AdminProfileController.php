<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        return Inertia::render('admin/Profile', [
            'user' => [
                'ho_ten' => $user->ho_ten,
                'email' => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'anh_dai_dien' => $user->anh_dai_dien,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        Log::info('Admin Profile Update', [
            'user_id' => $user->id,
            'has_avatar' => $request->hasFile('anh_dai_dien'),
            'all_files' => array_keys($_FILES),
            'content_type' => $request->header('Content-Type'),
        ]);

        // Validate basic info + avatar together
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'anh_dai_dien' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'anh_dai_dien.image' => 'File phải là hình ảnh.',
            'anh_dai_dien.mimes' => 'Chỉ hỗ trợ định dạng JPG, PNG, WEBP.',
            'anh_dai_dien.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ]);

        // Validate password if provided
        if ($request->filled('current_password') || $request->filled('password')) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $user->update([
                'mat_khau_hash' => Hash::make($request->password),
            ]);
        }

        // Update info
        $user->update([
            'ho_ten' => $request->input('ho_ten'),
            'so_dien_thoai' => $request->input('so_dien_thoai'),
        ]);

        // Avatar upload
        if ($request->hasFile('anh_dai_dien')) {
            $path = $request->file('anh_dai_dien')->store('avatars/' . $user->id, 'public');
            $user->update(['anh_dai_dien' => '/storage/' . $path]);
            Log::info('Admin avatar saved', ['path' => $path, 'user_id' => $user->id]);
        }

        return back()->with('success', 'Hồ sơ đã được cập nhật thành công!');
    }
}
