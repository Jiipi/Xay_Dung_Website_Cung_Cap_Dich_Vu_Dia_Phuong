<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\DonDatLich;
use App\Models\ThongBao;
use App\Repositories\Contracts\Booking\BookingRepositoryInterface;
use App\Repositories\Contracts\Review\ReviewRepositoryInterface;
use App\Services\Review\ReviewService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService,
        protected BookingRepositoryInterface $bookingRepository,
        protected ReviewRepositoryInterface $reviewRepository
    ) {}
    /**
     * Show the review form for a completed booking.
     */
    public function create(Request $request): Response
    {
        $bookingId = $request->query('booking_id');

        $booking = $this->bookingRepository->findByIdAndCustomer($bookingId, auth()->id());
        
        if (!$booking || $booking->trang_thai_don !== 'hoan_thanh') {
            abort(404);
        }

        // Check if already reviewed
        if ($this->reviewRepository->existsForBooking($booking->id)) {
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('error', 'Bạn đã đánh giá đơn này rồi.');
        }

        $hoso = $booking->nhaCungCap?->hoSoNhaCungCap;

        return Inertia::render('customer/reviews/Create', [
            'booking' => [
                'id'       => $booking->id,
                'code'     => $booking->ma_don,
                'service'  => $booking->dichVu?->ten_dich_vu ?? 'Dịch vụ',
                'provider' => $hoso?->ten_thuong_hieu ?? 'NCC',
                'date'     => $booking->thoi_gian_thuc_hien?->format('d/m/Y'),
                'total'    => (float) $booking->tong_tien,
            ],
        ]);
    }

    /**
     * Store a new review.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'don_dat_lich_id' => 'required|exists:don_dat_lich,id',
            'so_sao'          => 'required|integer|min:1|max:5',
            'noi_dung'        => 'nullable|string|max:2000',
            'an_danh'         => 'boolean',
        ]);

        try {
            $this->reviewService->createReview($data, auth()->id(), auth()->user());
            return redirect()->route('customer.bookings.index')
                ->with('success', 'Cảm ơn bạn đã đánh giá!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
