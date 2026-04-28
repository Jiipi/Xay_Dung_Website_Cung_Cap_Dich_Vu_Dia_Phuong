<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VaiTroNguoiDung;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('vaiTroNguoiDung');

        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('vaiTroNguoiDung', fn ($q) => $q->where('ten_vai_tro', $request->role));
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('trang_thai', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ho_ten', 'ilike', "%{$s}%")
                  ->orWhere('email', 'ilike', "%{$s}%")
                  ->orWhere('so_dien_thoai', 'ilike', "%{$s}%");
            });
        }

        $users = $query->latest()->paginate(15)->through(fn ($u) => [
            'id' => $u->id,
            'ho_ten' => $u->ho_ten,
            'email' => $u->email,
            'so_dien_thoai' => $u->so_dien_thoai,
            'anh_dai_dien' => $u->anh_dai_dien,
            'vai_tro' => $u->vaiTroNguoiDung?->ten_vai_tro ?? '—',
            'trang_thai' => $u->trang_thai,
            'ngay_tao' => $u->created_at?->format('d/m/Y'),
            'lan_dang_nhap_cuoi' => $u->lan_dang_nhap_cuoi,
        ]);

        $roles = VaiTroNguoiDung::all()->pluck('ten_vai_tro');

        $stats = [
            'total' => User::count(),
            'active' => User::where('trang_thai', 'hoat_dong')->count(),
            'inactive' => User::where('trang_thai', '!=', 'hoat_dong')->count(),
        ];

        return Inertia::render('admin/Users', [
            'users' => $users,
            'roles' => $roles,
            'stats' => $stats,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->trang_thai = $user->trang_thai === 'hoat_dong' ? 'bi_khoa' : 'hoat_dong';
        $user->save();

        return back()->with('success', "Đã cập nhật trạng thái người dùng #{$id}");
    }
}
