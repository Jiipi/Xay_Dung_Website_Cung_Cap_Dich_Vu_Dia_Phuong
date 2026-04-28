<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\DichVu;
use App\Models\DonDatLich;
use App\Models\ThongBao;
use App\Models\User;
use App\Models\YeuThich;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}
    /**
     * Customer dashboard with stats, reminders, upcoming bookings, and recommendations.
     */
    public function dashboard(): Response
    {
        $dashboardData = $this->dashboardService->getCustomerDashboard(auth()->id());

        return Inertia::render('customer/Dashboard', [
            'stats' => $dashboardData['stats'],
            'profile' => [
                'name' => $dashboardData['user']->name,
                'email' => $dashboardData['user']->email,
                'phone' => $dashboardData['user']->so_dien_thoai,
                'address' => $dashboardData['user']->dia_chi_chi_tiet,
            ],
            'recentBookings' => Inertia::defer(fn() => $dashboardData['recentBookings']->map(
                fn ($booking) => $this->mapBookingCard($booking)
            )),
            'upcomingBookings' => Inertia::defer(fn() => $dashboardData['upcomingBookings']->map(
                fn ($booking) => $this->mapBookingCard($booking)
            )),
            'recommendedServices' => Inertia::defer(fn() => $dashboardData['recommendedServices']->map(
                fn ($service) => $this->mapRecommendedService($service)
            )),
        ]);
    }

    /**
     * Booking history page.
     */
    public function bookings(): Response
    {
        $user = auth()->user();

        $bookings = DonDatLich::with(['dichVu', 'nhaCungCap'])
            ->where('khach_hang_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'code' => $b->ma_don,
                'service' => $b->dichVu?->ten_dich_vu ?? 'Dịch vụ',
                'provider' => $b->nhaCungCap?->name ?? 'NCC',
                'date' => $b->thoi_gian_thuc_hien?->format('d/m/Y') ?? '',
                'time' => $b->thoi_gian_thuc_hien?->format('H:i') ?? '',
                'status' => $b->trang_thai_don,
                'statusLabel' => $this->statusLabel($b->trang_thai_don),
                'price' => (float) $b->tong_tien,
                'image' => $this->getServiceImage($b->dichVu),
                'serviceId' => $b->dich_vu_id,
            ]);

        return Inertia::render('customer/bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Favorites page.
     */
    public function favorites(): Response
    {
        $user = auth()->user();

        $favorites = YeuThich::with(['dichVu.nhaCungCap.hoSoNhaCungCap'])
            ->where('nguoi_dung_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($fav) {
                $svc = $fav->dichVu;
                $hoso = $svc?->nhaCungCap?->hoSoNhaCungCap;

                return [
                    'id' => $fav->id,
                    'serviceId' => $svc?->id,
                    'title' => $svc?->ten_dich_vu ?? 'Dịch vụ',
                    'provider' => $hoso?->ten_thuong_hieu ?? 'NCC',
                    'price' => (float) ($svc?->gia_tu ?? 0),
                    'rating' => (float) ($hoso?->diem_danh_gia ?? 0),
                    'image' => $this->getServiceImage($svc),
                    'addedAt' => $fav->created_at?->format('d/m/Y'),
                ];
            });

        return Inertia::render('customer/favorites/Index', [
            'favorites' => $favorites,
        ]);
    }

    /**
     * Toggle favorite.
     */
    public function toggleFavorite(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $serviceId = $request->input('dich_vu_id');

        $existing = YeuThich::where('nguoi_dung_id', $user->id)
            ->where('dich_vu_id', $serviceId)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['favorited' => false]);
        }

        YeuThich::create([
            'nguoi_dung_id' => $user->id,
            'dich_vu_id' => $serviceId,
        ]);

        return response()->json(['favorited' => true]);
    }

    private function getServiceImage($dichVu): string
    {
        if ($dichVu && is_array($dichVu->danh_sach_anh) && count($dichVu->danh_sach_anh) > 0) {
            return $dichVu->danh_sach_anh[0];
        }

        return 'https://picsum.photos/seed/' . md5($dichVu?->id ?? 'default') . '/200/200';
    }

    private function mapBookingCard(DonDatLich $booking): array
    {
        return [
            'id' => $booking->id,
            'code' => $booking->ma_don,
            'service' => $booking->dichVu?->ten_dich_vu ?? 'Dịch vụ',
            'provider' => $this->resolveProviderName($booking->nhaCungCap, $booking->dichVu),
            'date' => $booking->thoi_gian_thuc_hien?->format('d/m/Y') ?? '',
            'time' => $booking->thoi_gian_thuc_hien?->format('H:i') ?? '',
            'status' => $booking->trang_thai_don,
            'statusLabel' => $this->statusLabel($booking->trang_thai_don),
            'price' => (float) $booking->tong_tien,
            'image' => $this->getServiceImage($booking->dichVu),
            'hasReview' => $booking->relationLoaded('danhGia')
                ? $booking->danhGia !== null
                : DanhGia::where('don_dat_lich_id', $booking->id)->exists(),
        ];
    }

    private function mapRecommendedService(DichVu $service): array
    {
        $provider = $service->nhaCungCap;
        $providerProfile = $provider?->hoSoNhaCungCap;
        $price = (float) ($service->gia_tu ?? 0);
        $rating = (float) ($providerProfile?->diem_danh_gia ?? 0);

        return [
            'id' => $service->id,
            'title' => $service->ten_dich_vu,
            'provider' => $this->resolveProviderName($provider, $service),
            'category' => $service->danhMuc?->ten_danh_muc ?? 'Dịch vụ địa phương',
            'price' => $price,
            'rating' => $rating,
            'image' => $this->getServiceImage($service),
            'location' => $service->dia_chi_hien_thi ?? 'Đà Lạt',
            'badge' => $rating >= 4.8 ? 'Đánh giá cao' : ($price > 0 && $price <= 300000 ? 'Giá dễ đặt' : 'Đề xuất cho bạn'),
        ];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'cho_xac_nhan' => 'Chờ xác nhận',
            'da_xac_nhan' => 'Đã xác nhận',
            'dang_thuc_hien' => 'Đang thực hiện',
            'hoan_thanh' => 'Hoàn thành',
            'da_huy' => 'Đã hủy',
            default => $status,
        };
    }

    private function resolveProviderName(?User $provider, ?DichVu $service = null): string
    {
        $profile = $service?->nhaCungCap?->hoSoNhaCungCap ?? $provider?->hoSoNhaCungCap;

        if ($profile?->ten_thuong_hieu) {
            return $profile->ten_thuong_hieu;
        }

        return $provider?->name ?? 'Nhà cung cấp địa phương';
    }
}
