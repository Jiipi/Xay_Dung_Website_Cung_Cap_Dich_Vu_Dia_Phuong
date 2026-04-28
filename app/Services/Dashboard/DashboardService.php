<?php

namespace App\Services\Dashboard;

use App\Models\DanhGia;
use App\Models\DichVu;
use App\Models\DonDatLich;
use App\Models\ThongBao;
use App\Models\User;
use App\Models\YeuThich;
use App\Models\VaiTroNguoiDung;
use App\Models\DanhMucDichVu;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function getCustomerDashboard(int $userId): array
    {
        $user = User::find($userId);

        $bookingQuery = DonDatLich::with([
            'danhGia',
            'dichVu.danhMuc',
            'dichVu.nhaCungCap.hoSoNhaCungCap',
            'nhaCungCap.hoSoNhaCungCap',
        ])->where('khach_hang_id', $user->id);

        $totalBookings = (clone $bookingQuery)->count();
        $completedBookings = (clone $bookingQuery)->where('trang_thai_don', 'hoan_thanh')->count();
        $pendingBookings = (clone $bookingQuery)->where('trang_thai_don', 'cho_xac_nhan')->count();
        $upcomingBookingsCount = (clone $bookingQuery)
            ->whereIn('trang_thai_don', ['cho_xac_nhan', 'da_xac_nhan', 'dang_thuc_hien'])
            ->whereNotNull('thoi_gian_thuc_hien')
            ->where('thoi_gian_thuc_hien', '>=', now())
            ->count();
        $reviewPendingCount = (clone $bookingQuery)
            ->where('trang_thai_don', 'hoan_thanh')
            ->doesntHave('danhGia')
            ->count();
        $totalFavorites = YeuThich::where('nguoi_dung_id', $user->id)->count();
        $unreadNotifications = ThongBao::where('nguoi_dung_id', $user->id)
            ->where('da_doc', false)
            ->count();

        $recentBookings = (clone $bookingQuery)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $upcomingBookings = (clone $bookingQuery)
            ->whereIn('trang_thai_don', ['cho_xac_nhan', 'da_xac_nhan', 'dang_thuc_hien'])
            ->whereNotNull('thoi_gian_thuc_hien')
            ->where('thoi_gian_thuc_hien', '>=', now())
            ->orderBy('thoi_gian_thuc_hien')
            ->limit(3)
            ->get();

        $favoriteRecords = YeuThich::with('dichVu')
            ->where('nguoi_dung_id', $user->id)
            ->get();

        $favoriteServiceIds = $favoriteRecords->pluck('dich_vu_id')->filter()->values();

        $interestCategoryIds = $favoriteRecords
            ->pluck('dichVu.danh_muc_id')
            ->merge((clone $bookingQuery)->get()->pluck('dichVu.danh_muc_id'))
            ->filter()
            ->unique()
            ->values();

        $recommendedQuery = DichVu::with(['danhMuc', 'nhaCungCap.hoSoNhaCungCap'])
            ->where('trang_thai_hoat_dong', 'hoat_dong');

        if ($interestCategoryIds->isNotEmpty()) {
            $recommendedQuery->whereIn('danh_muc_id', $interestCategoryIds);
        }

        if ($favoriteServiceIds->isNotEmpty()) {
            $recommendedQuery->whereNotIn('id', $favoriteServiceIds);
        }

        $recommendedServices = $recommendedQuery
            ->orderByDesc('do_uu_tien')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        if ($recommendedServices->isEmpty()) {
            $recommendedServices = DichVu::with(['danhMuc', 'nhaCungCap.hoSoNhaCungCap'])
                ->where('trang_thai_hoat_dong', 'hoat_dong')
                ->when($favoriteServiceIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $favoriteServiceIds))
                ->orderByDesc('do_uu_tien')
                ->orderByDesc('id')
                ->limit(3)
                ->get();
        }

        return [
            'stats' => compact('totalBookings', 'completedBookings', 'pendingBookings', 'upcomingBookingsCount', 'reviewPendingCount', 'totalFavorites', 'unreadNotifications'),
            'recentBookings' => $recentBookings,
            'upcomingBookings' => $upcomingBookings,
            'recommendedServices' => $recommendedServices,
            'user' => $user,
        ];
    }

    public function getProviderDashboard(int $providerId): array
    {
        $now = Carbon::now();

        $totalRevenue = DonDatLich::where('nha_cung_cap_id', $providerId)
            ->where('trang_thai_don', 'hoan_thanh')
            ->sum('tong_tien');

        $totalOrders = DonDatLich::where('nha_cung_cap_id', $providerId)->count();

        $ordersThisMonth = DonDatLich::where('nha_cung_cap_id', $providerId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $avgRating = DanhGia::where('nha_cung_cap_id', $providerId)->avg('so_sao') ?? 0;
        $totalReviews = DanhGia::where('nha_cung_cap_id', $providerId)->count();

        $pendingOrders = DonDatLich::where('nha_cung_cap_id', $providerId)
            ->where('trang_thai_don', 'cho_xac_nhan')
            ->count();

        $revenuePrevMonth = DonDatLich::where('nha_cung_cap_id', $providerId)
            ->where('trang_thai_don', 'hoan_thanh')
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->sum('tong_tien');

        $revenueChangePercent = $revenuePrevMonth > 0
            ? round(($totalRevenue - $revenuePrevMonth) / $revenuePrevMonth * 100)
            : ($totalRevenue > 0 ? 100 : 0);

        $ordersPrevMonth = DonDatLich::where('nha_cung_cap_id', $providerId)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->count();

        $ordersChangePercent = $ordersPrevMonth > 0
            ? round(($ordersThisMonth - $ordersPrevMonth) / $ordersPrevMonth * 100)
            : ($ordersThisMonth > 0 ? 100 : 0);

        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $revenue = DonDatLich::where('nha_cung_cap_id', $providerId)
                ->where('trang_thai_don', 'hoan_thanh')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('tong_tien');
            $orders = DonDatLich::where('nha_cung_cap_id', $providerId)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
            $revenueChart[] = [
                'month' => $month->translatedFormat('M'),
                'monthFull' => $month->translatedFormat('F Y'),
                'revenue' => (float) $revenue,
                'orders' => $orders,
            ];
        }

        $recentOrders = DonDatLich::with(['khachHang', 'dichVu'])
            ->where('nha_cung_cap_id', $providerId)
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'ma_don' => $order->ma_don,
                'khach_hang' => $order->khachHang->ho_ten ?? 'Ẩn danh',
                'dich_vu' => $order->dichVu->ten_dich_vu ?? '—',
                'ngay' => $order->created_at->format('d/m/Y'),
                'trang_thai' => $order->trang_thai_don,
                'tong_tien' => (float) $order->tong_tien,
            ]);

        $topServices = DichVu::where('nha_cung_cap_id', $providerId)
            ->withCount('donDatLich')
            ->orderByDesc('don_dat_lich_count')
            ->take(5)
            ->get()
            ->map(function ($svc) use ($providerId) {
                $avgStar = DanhGia::where('nha_cung_cap_id', $providerId)
                    ->whereHas('donDatLich', fn ($q) => $q->where('dich_vu_id', $svc->id))
                    ->avg('so_sao') ?? 0;
                return [
                    'id' => $svc->id,
                    'ten_dich_vu' => $svc->ten_dich_vu,
                    'so_don' => $svc->don_dat_lich_count,
                    'rating' => round($avgStar, 1),
                ];
            });

        $upcomingAppointments = DonDatLich::with(['khachHang', 'dichVu'])
            ->where('nha_cung_cap_id', $providerId)
            ->whereNotIn('trang_thai_don', ['da_huy', 'hoan_thanh'])
            ->whereBetween('thoi_gian_thuc_hien', [$now->copy()->startOfDay(), $now->copy()->addDays(7)->endOfDay()])
            ->orderBy('thoi_gian_thuc_hien')
            ->take(5)
            ->get()
            ->map(fn ($apt) => [
                'id' => $apt->id,
                'khach_hang' => $apt->khachHang->ho_ten ?? 'Ẩn danh',
                'dich_vu' => $apt->dichVu->ten_dich_vu ?? '—',
                'thoi_gian' => $apt->thoi_gian_thuc_hien?->format('H:i d/m'),
                'dia_diem' => $apt->dia_diem_thuc_hien,
                'trang_thai' => $apt->trang_thai_don,
            ]);

        $latestReviews = DanhGia::with('khachHang')
            ->where('nha_cung_cap_id', $providerId)
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn ($review) => [
                'id' => $review->id,
                'khach_hang' => $review->an_danh ? 'Khách ẩn danh' : ($review->khachHang->ho_ten ?? 'Ẩn danh'),
                'avatar' => $review->an_danh ? null : $review->khachHang?->anh_dai_dien,
                'so_sao' => $review->so_sao,
                'noi_dung' => $review->noi_dung,
                'ngay' => $review->created_at->format('d/m/Y'),
                'phan_hoi' => $review->phan_hoi_tu_ncc,
            ]);

        return [
            'kpiCards' => [
                'totalRevenue' => (float) $totalRevenue,
                'revenueChangePercent' => $revenueChangePercent,
                'totalOrders' => $totalOrders,
                'ordersThisMonth' => $ordersThisMonth,
                'ordersChangePercent' => $ordersChangePercent,
                'avgRating' => round($avgRating, 1),
                'totalReviews' => $totalReviews,
                'pendingOrders' => $pendingOrders,
            ],
            'revenueChart' => $revenueChart,
            'recentOrders' => $recentOrders,
            'topServices' => $topServices,
            'upcomingAppointments' => $upcomingAppointments,
            'latestReviews' => $latestReviews,
        ];
    }

    public function getAdminDashboard(): array
    {
        $now = Carbon::now();

        $totalUsers = User::count();
        $usersLastWeek = User::where('created_at', '>=', $now->copy()->subWeek())->count();
        $usersPrevWeek = User::whereBetween('created_at', [
            $now->copy()->subWeeks(2), $now->copy()->subWeek(),
        ])->count();
        $usersChangePercent = $usersPrevWeek > 0
            ? round(($usersLastWeek - $usersPrevWeek) / $usersPrevWeek * 100)
            : ($usersLastWeek > 0 ? 100 : 0);

        $totalServices = DichVu::count();
        $pendingServices = DichVu::where('trang_thai_duyet', 'cho_duyet')->count();

        $totalRevenue = (float) DonDatLich::where('trang_thai_don', 'hoan_thanh')->sum('tong_tien');
        $revenueThisMonth = (float) DonDatLich::where('trang_thai_don', 'hoan_thanh')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('tong_tien');
        $revenueLastMonth = (float) DonDatLich::where('trang_thai_don', 'hoan_thanh')
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->sum('tong_tien');
        $revenueChangePercent = $revenueLastMonth > 0
            ? round(($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth * 100)
            : ($revenueThisMonth > 0 ? 100 : 0);

        $pendingOrders = DonDatLich::where('trang_thai_don', 'cho_xac_nhan')->count();

        $revenueChart = collect(range(5, 0))->map(function ($i) use ($now) {
            $month = $now->copy()->subMonths($i);
            return [
                'month' => $month->format('M'),
                'monthFull' => $month->format('F Y'),
                'revenue' => (float) DonDatLich::where('trang_thai_don', 'hoan_thanh')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('tong_tien'),
                'bookings' => DonDatLich::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        })->values();

        $roles = VaiTroNguoiDung::withCount('nguoiDung')->get();
        $userDistribution = $roles->map(fn ($role) => [
            'label' => $role->ten_vai_tro,
            'count' => $role->nguoi_dung_count,
        ]);

        $orderStatuses = DonDatLich::selectRaw('trang_thai_don, COUNT(*) as total')
            ->groupBy('trang_thai_don')
            ->pluck('total', 'trang_thai_don');

        $recentOrders = DonDatLich::with(['khachHang', 'dichVu'])
            ->latest()
            ->take(8)
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'ma_don' => 'DH-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'khach_hang' => $order->khachHang?->ho_ten ?? '—',
                'dich_vu' => $order->dichVu?->ten_dich_vu ?? '—',
                'ngay' => $order->created_at?->format('d/m/Y'),
                'trang_thai' => $order->trang_thai_don,
                'tong_tien' => (float) $order->tong_tien,
            ]);

        $recentUsers = User::with('vaiTroNguoiDung')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'ho_ten' => $u->ho_ten,
                'email' => $u->email,
                'vai_tro' => $u->vaiTroNguoiDung?->ten_vai_tro ?? '—',
                'ngay_tao' => $u->created_at?->format('d/m/Y'),
            ]);

        return [
            'kpiCards' => [
                'totalUsers' => $totalUsers,
                'usersChangePercent' => $usersChangePercent,
                'totalServices' => $totalServices,
                'pendingServices' => $pendingServices,
                'totalRevenue' => $totalRevenue,
                'revenueChangePercent' => $revenueChangePercent,
                'pendingOrders' => $pendingOrders,
            ],
            'revenueChart' => $revenueChart,
            'userDistribution' => $userDistribution,
            'orderStatuses' => $orderStatuses,
            'recentOrders' => $recentOrders,
            'recentUsers' => $recentUsers,
        ];
    }

    public function getAdminStats(): array
    {
        $now = Carbon::now();

        $revenueByMonth = collect(range(11, 0))->map(function ($i) use ($now) {
            $month = $now->copy()->subMonths($i);
            return [
                'month' => $month->format('M Y'),
                'revenue' => (float) DonDatLich::where('trang_thai_don', 'hoan_thanh')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('tong_tien'),
                'orders' => DonDatLich::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        })->values();

        $topServices = DichVu::withCount('donDatLich')
            ->orderByDesc('don_dat_lich_count')
            ->take(10)
            ->get()
            ->map(fn ($s) => [
                'ten_dich_vu' => $s->ten_dich_vu,
                'so_don' => $s->don_dat_lich_count,
                'doanh_thu' => (float) $s->donDatLich()->where('trang_thai_don', 'hoan_thanh')->sum('tong_tien'),
            ]);

        $usersByMonth = collect(range(5, 0))->map(function ($i) use ($now) {
            $month = $now->copy()->subMonths($i);
            return [
                'month' => $month->format('M'),
                'count' => User::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        })->values();

        $categoryStats = DanhMucDichVu::withCount('dichVu')
            ->orderByDesc('dich_vu_count')
            ->get()
            ->map(fn ($c) => [
                'ten_danh_muc' => $c->ten_danh_muc,
                'so_dich_vu' => $c->dich_vu_count,
            ]);

        $generalStats = [
            'totalRevenue' => (float) DonDatLich::where('trang_thai_don', 'hoan_thanh')->sum('tong_tien'),
            'totalOrders' => DonDatLich::count(),
            'completedOrders' => DonDatLich::where('trang_thai_don', 'hoan_thanh')->count(),
            'cancelledOrders' => DonDatLich::where('trang_thai_don', 'da_huy')->count(),
            'avgOrderValue' => (float) DonDatLich::where('trang_thai_don', 'hoan_thanh')->avg('tong_tien'),
            'totalReviews' => DanhGia::count(),
            'avgRating' => round((float) DanhGia::avg('so_sao'), 1),
        ];

        return [
            'revenueByMonth' => $revenueByMonth,
            'topServices' => $topServices,
            'usersByMonth' => $usersByMonth,
            'categoryStats' => $categoryStats,
            'generalStats' => $generalStats,
        ];
    }
}
