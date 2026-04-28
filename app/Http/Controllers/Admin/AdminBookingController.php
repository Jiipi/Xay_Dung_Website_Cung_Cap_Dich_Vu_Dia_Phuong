<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonDatLich;
use App\Models\ThongBao;
use App\Repositories\Contracts\Booking\BookingRepositoryInterface;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class AdminBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepositoryInterface $bookingRepository
    ) {}
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $bookings = $this->bookingRepository->getPaginatedForAdmin($status, $search, 15)
            ->withQueryString()
            ->through(fn ($b) => [
                'id' => $b->id,
                'ma_don' => $b->ma_don,
                'khach_hang' => $b->khachHang?->ho_ten ?? 'Ẩn danh',
                'nha_cung_cap' => $b->nhaCungCap?->ho_ten ?? '—',
                'dich_vu' => $b->dichVu?->ten_dich_vu ?? '—',
                'ngay_dat' => $b->created_at?->format('d/m/Y'),
                'thoi_gian_thuc_hien' => $b->thoi_gian_thuc_hien?->format('H:i d/m/Y'),
                'tong_tien' => (float) $b->tong_tien,
                'trang_thai' => $b->trang_thai_don,
                'trang_thai_thanh_toan' => $b->trang_thai_thanh_toan,
            ]);

        $statusCounts = $this->bookingRepository->getStatusCountsForAdmin();

        return Inertia::render('admin/Bookings', [
            'bookings' => $bookings,
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function forceConfirm(Request $request, $id)
    {
        try {
            $this->bookingService->adminForceConfirm($id);
            return back()->with('success', 'Đã ép xác nhận đơn hàng');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function forceComplete(Request $request, $id)
    {
        try {
            $this->bookingService->adminForceComplete($id);
            return back()->with('success', 'Đã ép hoàn thành đơn hàng');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function forceReject(Request $request, $id)
    {
        try {
            $this->bookingService->adminForceReject($id);
            return back()->with('success', 'Đã hủy đơn hàng');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
