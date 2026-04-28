<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Repositories\Contracts\Review\ReviewRepositoryInterface;
use App\Services\Review\ReviewService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class ProviderReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService,
        protected ReviewRepositoryInterface $reviewRepository
    ) {}
    /**
     * Danh sách đánh giá nhận được bởi provider.
     */
    public function index(Request $request)
    {
        $stars = $request->input('so_sao');
        $unreplied = $request->boolean('chua_phan_hoi');

        $reviews = $this->reviewRepository->getPaginatedForProvider(auth()->id(), $stars, $unreplied, 10)
            ->withQueryString()
            ->through(fn ($review) => [
                'id' => $review->id,
                'khach_hang' => $review->an_danh
                    ? 'Khách ẩn danh'
                    : ($review->khachHang?->ho_ten ?? 'Ẩn danh'),
                'avatar' => $review->an_danh ? null : $review->khachHang?->anh_dai_dien,
                'so_sao' => $review->so_sao,
                'noi_dung' => $review->noi_dung,
                'phan_hoi_tu_ncc' => $review->phan_hoi_tu_ncc,
                'ngay_phan_hoi' => $review->ngay_phan_hoi?->format('d/m/Y'),
                'dich_vu' => $review->donDatLich?->dichVu?->ten_dich_vu ?? '—',
                'ngay' => $review->created_at?->format('d/m/Y'),
            ]);

        $stats = $this->reviewRepository->getStatsForProvider(auth()->id());

        return Inertia::render('provider/reviews/Index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'filters' => $request->only(['so_sao', 'chua_phan_hoi']),
        ]);
    }

    /**
     * Phản hồi đánh giá.
     */
    public function reply(Request $request, int $id)
    {
        $request->validate([
            'phan_hoi' => 'required|string|max:1000',
        ]);

        try {
            $this->reviewService->replyToReview($id, auth()->id(), $request->input('phan_hoi'));
            return back()->with('success', 'Phản hồi đã được gửi thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
