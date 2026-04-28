<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateBookingRequest;
use App\Models\DichVu;
use App\Models\DonDatLich;
use App\Models\ThongBao;
use App\Repositories\Contracts\Booking\BookingRepositoryInterface;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepositoryInterface $bookingRepository
    ) {}
    /**
     * Store a new booking.
     */
    public function store(CreateBookingRequest $request): RedirectResponse
    {
        $result = $this->bookingService->createBooking($request->validated(), auth()->id());

        return redirect()->route('customer.bookings.success', $result['booking_id'])
            ->with('success', 'Bạn đã đặt lịch thành công!');
    }

    /**
     * Booking success page — shown right after booking.
     */
    public function success(int $id): Response
    {
        $booking = $this->bookingRepository->findByIdAndCustomer($id, auth()->id());
        if (!$booking) abort(404);

        $hoso = $booking->nhaCungCap?->hoSoNhaCungCap;

        return Inertia::render('customer/bookings/Success', [
            'booking' => [
                'id'        => $booking->id,
                'code'      => $booking->ma_don,
                'service'   => $booking->dichVu?->ten_dich_vu ?? 'Dịch vụ',
                'provider'  => $hoso?->ten_thuong_hieu ?? $booking->nhaCungCap?->ho_ten ?? 'NCC',
                'date'      => $booking->thoi_gian_thuc_hien?->format('d/m/Y'),
                'time'      => $booking->thoi_gian_thuc_hien?->format('H:i'),
                'address'   => $booking->dia_diem_thuc_hien,
                'quantity'  => (float) $booking->so_luong,
                'unit'      => $booking->don_vi,
                'total'     => (float) $booking->tong_tien,
                'status'    => $booking->trang_thai_don,
                'note'      => $booking->ghi_chu,
            ],
        ]);
    }

    /**
     * Booking detail page.
     */
    public function show(int $id): Response
    {
        $booking = $this->bookingRepository->findByIdAndCustomer($id, auth()->id());
        if (!$booking) abort(404);

        $hoso = $booking->nhaCungCap?->hoSoNhaCungCap;

        return Inertia::render('customer/bookings/Show', [
            'booking' => [
                'id'           => $booking->id,
                'code'         => $booking->ma_don,
                'service'      => $booking->dichVu?->ten_dich_vu ?? 'Dịch vụ',
                'serviceId'    => $booking->dich_vu_id,
                'provider'     => $hoso?->ten_thuong_hieu ?? $booking->nhaCungCap?->ho_ten ?? 'NCC',
                'providerId'   => $booking->nha_cung_cap_id,
                'providerAvatar' => $booking->nhaCungCap?->anh_dai_dien,
                'date'         => $booking->thoi_gian_thuc_hien?->format('d/m/Y'),
                'time'         => $booking->thoi_gian_thuc_hien?->format('H:i'),
                'address'      => $booking->dia_diem_thuc_hien,
                'quantity'     => (float) $booking->so_luong,
                'unit'         => $booking->don_vi,
                'subtotal'     => (float) $booking->tam_tinh,
                'fee'          => (float) $booking->phi_dich_vu,
                'discount'     => (float) $booking->giam_gia,
                'total'        => (float) $booking->tong_tien,
                'status'       => $booking->trang_thai_don,
                'paymentStatus'=> $booking->trang_thai_thanh_toan,
                'paymentMethod'=> $booking->phuong_thuc_thanh_toan,
                'note'         => $booking->ghi_chu,
                'cancelledBy'  => $booking->huy_boi,
                'cancelReason' => $booking->ly_do_huy,
                'hasReview'    => $booking->danhGia !== null,
                'image'        => $this->getServiceImage($booking->dichVu),
                'createdAt'    => $booking->created_at?->format('d/m/Y H:i'),
                'confirmedAt'  => null, // TODO: add column if needed
            ],
        ]);
    }

    /**
     * Cancel a booking (customer side).
     */
    public function cancel(int $id, Request $request): RedirectResponse
    {
        try {
            $this->bookingService->customerCancelBooking($id, auth()->id(), $request->input('ly_do', 'Khách hàng hủy đơn'));
            return redirect()->route('customer.bookings.index')
                ->with('success', 'Đã hủy đơn thành công.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function getServiceImage($dichVu): string
    {
        if ($dichVu && is_array($dichVu->danh_sach_anh) && count($dichVu->danh_sach_anh) > 0) {
            return $dichVu->danh_sach_anh[0];
        }
        return 'https://picsum.photos/seed/' . md5($dichVu?->id ?? 'default') . '/200/200';
    }
}
