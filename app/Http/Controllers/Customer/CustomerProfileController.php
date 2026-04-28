<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->so_dien_thoai,
                'address' => $user->dia_chi,
                'avatar'  => $user->anh_dai_dien,
            ],
        ]);
    }

    /**
     * Update the customer profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'name.max'      => 'Tên không được quá 255 ký tự.',
            'phone.max'     => 'Số điện thoại không hợp lệ.',
            'address.max'   => 'Địa chỉ quá dài.',
        ]);

        $user = auth()->user();
        $user->update([
            'name'          => $validated['name'],
            'so_dien_thoai' => $validated['phone'] ?? null,
            'dia_chi'       => $validated['address'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Hồ sơ đã được cập nhật!');
    }
}
