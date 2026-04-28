<?php

namespace App\Services\Review;

use App\Models\DonDatLich;
use App\Models\ThongBao;
use App\Models\User;
use App\Repositories\Contracts\Review\ReviewRepositoryInterface;
use Exception;

class ReviewService
{
    public function __construct(
        protected ReviewRepositoryInterface $reviewRepository
    ) {}

    public function createReview(array $data, int $customerId, User $customer): array
    {
        // Must verify the booking belongs to the customer and is completed
        $booking = DonDatLich::where('khach_hang_id', $customerId)
            ->where('trang_thai_don', 'hoan_thanh')
            ->findOrFail($data['don_dat_lich_id']);

        // Check duplicate
        if ($this->reviewRepository->existsForBooking($booking->id)) {
            throw new Exception("Bạn đã đánh giá đơn này rồi.");
        }

        $review = $this->reviewRepository->create([
            'don_dat_lich_id' => $booking->id,
            'nha_cung_cap_id' => $booking->nha_cung_cap_id,
            'khach_hang_id'   => $customerId,
            'so_sao'          => $data['so_sao'],
            'noi_dung'        => $data['noi_dung'] ?? null,
            'an_danh'         => $data['an_danh'] ?? false,
        ]);

        // Update provider rating average
        $this->updateProviderAverageRating($booking->nha_cung_cap_id);

        // Notify provider
        ThongBao::create([
            'nguoi_dung_id'  => $booking->nha_cung_cap_id,
            'tieu_de'        => 'Có đánh giá mới',
            'noi_dung'       => "{$customer->ho_ten} đã đánh giá {$data['so_sao']}★ cho đơn {$booking->ma_don}.",
            'loai_thong_bao' => 'review_new',
            'da_doc'         => false,
        ]);

        return [
            'success' => true,
            'review_id' => $review->id,
        ];
    }

    public function replyToReview(int $reviewId, int $providerId, string $replyContent): bool
    {
        $review = $this->reviewRepository->findByIdAndProvider($reviewId, $providerId);

        if (!$review) {
            throw new Exception("Không tìm thấy đánh giá hoặc bạn không có quyền phản hồi.");
        }

        return $this->reviewRepository->update($reviewId, [
            'phan_hoi_tu_ncc' => $replyContent,
            'ngay_phan_hoi' => now(),
        ]);
    }

    public function deleteReviewByAdmin(int $reviewId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);
        
        if (!$review) {
            throw new Exception("Không tìm thấy đánh giá.");
        }

        $providerId = $review->nha_cung_cap_id;
        
        // Delete review
        $this->reviewRepository->delete($reviewId);

        // Recalculate provider average rating
        $this->updateProviderAverageRating($providerId);

        return true;
    }

    private function updateProviderAverageRating(int $providerId): void
    {
        $avg = $this->reviewRepository->calculateAverageRatingForProvider($providerId);
        
        $provider = User::find($providerId);
        if ($provider && $provider->hoSoNhaCungCap) {
            $provider->hoSoNhaCungCap->update(['diem_danh_gia' => $avg]);
        }
    }
}
