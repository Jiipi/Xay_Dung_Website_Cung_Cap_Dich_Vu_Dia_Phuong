<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\DonDatLich;
use App\Models\ThongBao;
use App\Repositories\Contracts\Booking\BookingRepositoryInterface;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class ProviderBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepositoryInterface $bookingRepository
    ) {}
    /**
     * Danh sách booking của provider.
     */
    public function index(Request $request)
    {
        $status = $request->input('trang_thai');
        $search = $request->input('search');
        $statusCounts = $this->bookingRepository->getStatusCountsForProvider(auth()->id());
        
        $bookings = $this->bookingRepository->getPaginatedForProvider(auth()->id(), $status, $search, 10)
            ->withQueryString()
            ->through(fn (DonDatLich $booking) => [
                'id' => $booking->id,
                'ma_don' => $booking->ma_don,
                'khach_hang' => $booking->khachHang?->ho_ten ?? 'Ẩn danh',
                'so_dien_thoai' => $booking->khachHang?->so_dien_thoai,
                'dich_vu' => $booking->dichVu?->ten_dich_vu ?? '—',
                'ngay_dat' => $booking->created_at?->format('d/m/Y'),
                'thoi_gian_thuc_hien' => $booking->thoi_gian_thuc_hien?->format('H:i d/m/Y'),
                'dia_diem' => $booking->dia_diem_thuc_hien,
                'so_luong' => (float) $booking->so_luong,
                'don_vi' => $booking->don_vi,
                'ghi_chu' => $booking->ghi_chu,
                'tong_tien' => (float) $booking->tong_tien,
                'trang_thai' => $booking->trang_thai_don,
                'trang_thai_thanh_toan' => $booking->trang_thai_thanh_toan,
            ]);

        return Inertia::render('provider/Bookings', [
            'bookings' => $bookings,
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['trang_thai', 'search']),
        ]);
    }

    /**
     * Chi tiết 1 booking.
     */
    public function show(Request $request, int $id)
    {
        $booking = $this->bookingRepository->findByIdAndProvider($id, auth()->id());
        if (!$booking) abort(404);

        return Inertia::render('provider/bookings/Show', [
            'booking' => [
                'id' => $booking->id,
                'ma_don' => $booking->ma_don,
                'khach_hang' => [
                    'ho_ten' => $booking->khachHang?->ho_ten ?? 'Ẩn danh',
                    'email' => $booking->khachHang?->email,
                    'so_dien_thoai' => $booking->khachHang?->so_dien_thoai,
                    'anh_dai_dien' => $booking->khachHang?->anh_dai_dien,
                ],
                'dich_vu' => [
                    'id' => $booking->dichVu?->id,
                    'ten_dich_vu' => $booking->dichVu?->ten_dich_vu ?? '—',
                    'gia_tu' => (float) ($booking->dichVu?->gia_tu ?? 0),
                ],
                'thoi_gian_thuc_hien' => $booking->thoi_gian_thuc_hien?->format('H:i d/m/Y'),
                'dia_diem_thuc_hien' => $booking->dia_diem_thuc_hien,
                'so_luong' => (float) $booking->so_luong,
                'don_vi' => $booking->don_vi,
                'ghi_chu' => $booking->ghi_chu,
                'tam_tinh' => (float) $booking->tam_tinh,
                'phi_dich_vu' => (float) $booking->phi_dich_vu,
                'giam_gia' => (float) $booking->giam_gia,
                'tong_tien' => (float) $booking->tong_tien,
                'trang_thai' => $booking->trang_thai_don,
                'trang_thai_thanh_toan' => $booking->trang_thai_thanh_toan,
                'phuong_thuc_thanh_toan' => $booking->phuong_thuc_thanh_toan,
                'ngay_dat' => $booking->created_at?->format('H:i d/m/Y'),
                'danh_gia' => $booking->danhGia ? [
                    'so_sao' => $booking->danhGia->so_sao,
                    'noi_dung' => $booking->danhGia->noi_dung,
                    'phan_hoi_tu_ncc' => $booking->danhGia->phan_hoi_tu_ncc,
                ] : null,
            ],
        ]);
    }

    /**
     * Xác nhận booking.
     */
    public function confirm(Request $request, int $id)
    {
        try {
            $this->bookingService->providerConfirmBooking($id, auth()->id());
            return back()->with('success', 'Đã xác nhận booking');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Từ chối booking.
     */
    public function reject(Request $request, int $id)
    {
        $request->validate([
            'ly_do' => 'nullable|string|max:500',
        ]);

        try {
            $this->bookingService->providerRejectBooking($id, auth()->id(), $request->input('ly_do') ?? 'Không có lý do');
            return back()->with('success', 'Đã từ chối booking');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Hoàn thành booking.
     */
    public function complete(Request $request, int $id)
    {
        try {
            $this->bookingService->providerCompleteBooking($id, auth()->id());
            return back()->with('success', 'Booking đã hoàn thành!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
