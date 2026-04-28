<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Repositories\Contracts\Review\ReviewRepositoryInterface;
use App\Services\Review\ReviewService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class AdminReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService,
        protected ReviewRepositoryInterface $reviewRepository
    ) {}
    public function index(Request $request)
    {
        $stars = $request->input('so_sao') === 'all' ? null : $request->input('so_sao');
        $search = $request->input('search');

        $reviews = $this->reviewRepository->getPaginatedForAdmin($stars, $search, 15)
            ->withQueryString()
            ->through(fn ($r) => [
                'id' => $r->id,
                'khach_hang' => $r->an_danh ? 'Khách ẩn danh' : ($r->khachHang?->ho_ten ?? 'Ẩn danh'),
                'nha_cung_cap' => $r->nhaCungCap?->ho_ten ?? '—',
                'dich_vu' => $r->donDatLich?->dichVu?->ten_dich_vu ?? '—',
                'so_sao' => $r->so_sao,
                'noi_dung' => $r->noi_dung,
                'phan_hoi' => $r->phan_hoi_tu_ncc,
                'ngay_tao' => $r->created_at?->format('H:i d/m/Y'),
            ]);

        $stats = $this->reviewRepository->getStatsForAdmin();

        return Inertia::render('admin/Reviews', [
            'reviews' => $reviews,
            'stats' => $stats,
            'filters' => $request->only(['search', 'so_sao']),
        ]);
    }

    public function destroy($id)
    {
        try {
            $this->reviewService->deleteReviewByAdmin($id);
            return back()->with('success', 'Đã xóa đánh giá vi phạm thành công.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
