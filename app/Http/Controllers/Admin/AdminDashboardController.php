<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DichVu;
use App\Models\DonDatLich;
use App\Models\User;
use App\Models\VaiTroNguoiDung;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}
    public function index(Request $request)
    {
        $dashboardData = $this->dashboardService->getAdminDashboard();

        return Inertia::render('admin/Dashboard', [
            'kpiCards' => $dashboardData['kpiCards'],
            'revenueChart' => $dashboardData['revenueChart'],
            'userDistribution' => $dashboardData['userDistribution'],
            'orderStatuses' => $dashboardData['orderStatuses'],
            'recentOrders' => $dashboardData['recentOrders'],
            'recentUsers' => $dashboardData['recentUsers'],
        ]);
    }
}
