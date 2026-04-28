<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\DichVu;
use App\Models\DonDatLich;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderDashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}
    public function index(Request $request)
    {
        $dashboardData = $this->dashboardService->getProviderDashboard(auth()->id());
        $hoSo = auth()->user()->hoSoNhaCungCap;

        return Inertia::render('provider/Dashboard', [
            'kpiCards' => $dashboardData['kpiCards'],
            'revenueChart' => $dashboardData['revenueChart'],
            'recentOrders' => $dashboardData['recentOrders'],
            'topServices' => $dashboardData['topServices'],
            'maxServiceOrders' => $dashboardData['topServices']->max('so_don') ?: 1,
            'upcomingAppointments' => $dashboardData['upcomingAppointments'],
            'latestReviews' => $dashboardData['latestReviews'],
            'providerProfile' => $hoSo ? [
                'ten_thuong_hieu' => $hoSo->ten_thuong_hieu,
                'diem_danh_gia' => $hoSo->diem_danh_gia,
            ] : null,
        ]);
    }
}
