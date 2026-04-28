<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\DichVu;
use App\Models\DonDatLich;
use App\Models\User;
use App\Models\DanhMucDichVu;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Inertia\Inertia;

class AdminStatsController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}
    public function index()
    {
        $statsData = $this->dashboardService->getAdminStats();

        return Inertia::render('admin/Stats', [
            'revenueByMonth' => $statsData['revenueByMonth'],
            'topServices' => $statsData['topServices'],
            'usersByMonth' => $statsData['usersByMonth'],
            'categoryStats' => $statsData['categoryStats'],
            'generalStats' => $statsData['generalStats'],
        ]);
    }
}
