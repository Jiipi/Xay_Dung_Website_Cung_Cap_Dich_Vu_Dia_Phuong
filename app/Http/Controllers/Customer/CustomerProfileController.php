<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class CustomerProfileController extends Controller
{
    /**
     * Show the customer profile form.
     */
    public function edit(): Response
    {
        $user = auth()->user();

        return Inertia::render('customer/Profile', [
            'profile' => [
                'name'    => $user->ho_ten,
                'email'   => $user->email,
                'phone'   => $user->so_dien_thoai,
                'address' => $user->dia_chi_chi_tiet,
                'avatar'  => $user->anh_dai_dien,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        Log::info('Customer Profile Update', [
            'user_id' => $user->id,
            'has_avatar' => $request->hasFile('avatar'),
            'all_files' => array_keys($_FILES),
            'content_type' => $request->header('Content-Type'),
        ]);

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'name.max'      => 'Tên không được quá 255 ký tự.',
            'phone.max'     => 'Số điện thoại không hợp lệ.',
            'address.max'   => 'Địa chỉ quá dài.',
            'avatar.image'  => 'File phải là hình ảnh.',
            'avatar.mimes'  => 'Chỉ hỗ trợ định dạng JPG, PNG, WEBP.',
            'avatar.max'    => 'Kích thước ảnh không được vượt quá 2MB.',
        ]);

        // Validate and update password if provided
        if ($request->filled('current_password') || $request->filled('password')) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $user->update([
                'mat_khau_hash' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars/' . $user->id, 'public');
            $user->update(['anh_dai_dien' => '/storage/' . $path]);
            Log::info('Customer avatar saved', ['path' => $path, 'user_id' => $user->id]);
        }

        $user->update([
            'ho_ten'           => $validated['name'],
            'so_dien_thoai'    => $validated['phone'] ?? null,
            'dia_chi_chi_tiet' => $validated['address'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Hồ sơ đã được cập nhật thành công!');
    }
}
