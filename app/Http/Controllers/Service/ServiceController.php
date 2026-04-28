<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\AI\GenerateAiPlanRequest;
use App\Http\Requests\AI\PlannerChatRequest;
use App\Models\DanhGia;
use App\Models\DanhMucDichVu;
use App\Models\DichVu;
use App\Models\YeuThich;
use App\Repositories\Contracts\Service\ServiceRepositoryInterface;
use App\Services\AI\GeminiItineraryPlannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceRepositoryInterface $serviceRepository
    ) {}
    /**
     * Display the public service catalog (Khám phá).
     */
    public function index(Request $request): Response
    {
        // 1. Lấy tất cả dịch vụ đang hoạt động
        $servicesRaw = $this->serviceRepository->getActivePublicServices();

        // Pre-load review counts per provider (tránh N+1)
        $providerIds = $servicesRaw->pluck('nha_cung_cap_id')->unique();
        $reviewCounts = DanhGia::whereIn('nha_cung_cap_id', $providerIds)
            ->selectRaw('nha_cung_cap_id, count(*) as total')
            ->groupBy('nha_cung_cap_id')
            ->pluck('total', 'nha_cung_cap_id');

        $userFavorites = auth()->check() 
            ? YeuThich::where('nguoi_dung_id', auth()->id())->pluck('dich_vu_id')->toArray() 
            : [];

        $services = $servicesRaw->map(function ($svc) use ($reviewCounts, $userFavorites) {
            $hoso = $svc->nhaCungCap?->hoSoNhaCungCap;
            $reviewCount = $reviewCounts[$svc->nha_cung_cap_id] ?? 0;

            // Xác định category slug (parent category)
            $categorySlug = $svc->danhMuc?->parent?->slug ?? $svc->danhMuc?->slug ?? 'khac';

            // Format giá
            $price = (float) $svc->gia_tu;

            // Image
            $image = (is_array($svc->danh_sach_anh) && count($svc->danh_sach_anh) > 0)
                ? $svc->danh_sach_anh[0]
                : 'https://picsum.photos/seed/' . md5($svc->id) . '/600/400';

            // Badge
            $badge = null;
            if ($reviewCount > 10) {
                $badge = 'Phổ biến';
            } elseif ($price < 200000) {
                $badge = 'Giá tốt';
            } elseif (($hoso?->diem_danh_gia ?? 0) >= 4.5) {
                $badge = 'Top đánh giá';
            }

            return [
                'id' => $svc->id,
                'title' => $svc->ten_dich_vu,
                'provider' => $hoso?->ten_thuong_hieu ?? 'Nhà cung cấp',
                'rating' => (float) ($hoso?->diem_danh_gia ?? 0),
                'reviews' => $reviewCount,
                'price' => $price,
                'category' => $categorySlug,
                'location' => $svc->dia_chi_hien_thi ?? 'Đà Lạt',
                'image' => $image,
                'badge' => $badge,
                'is_favorited' => in_array($svc->id, $userFavorites),
            ];
        })->values()->all();

        // 2. Categories cho sidebar filter
        $parentCategories = DanhMucDichVu::whereNull('parent_id')
            ->where('trang_thai', 'hoat_dong')
            ->orderBy('thu_tu_hien_thi')
            ->get();

        $categories = $parentCategories->map(function ($cat) use ($servicesRaw) {
            // Đếm dịch vụ thuộc category cha hoặc các category con
            $childIds = $cat->children()->pluck('id')->push($cat->id);
            $count = $servicesRaw->whereIn('danh_muc_id', $childIds)->count();

            return [
                'value' => $cat->slug,
                'label' => $cat->ten_danh_muc,
                'count' => $count,
            ];
        })->values()->all();

        // 3. Locations unique cho sidebar
        $locations = collect($services)
            ->pluck('location')
            ->map(fn ($loc) => trim(last(explode("\n", $loc))))
            ->unique()
            ->filter()
            ->values()
            ->map(fn ($loc) => [
                'value' => $loc,
                'count' => collect($services)->filter(fn ($s) => str_contains($s['location'], $loc))->count(),
            ])
            ->sortByDesc('count')
            ->values()
            ->take(10)
            ->all();

        return Inertia::render('services/Index', [
            'services' => $services,
            'categories' => $categories,
            'locations' => $locations,
            'queryCategory' => $request->query('category'),
            'querySearch' => $request->query('q'),
        ]);
    }

    /**
     * Display a single service detail (Chi tiết & Đặt lịch).
     */
    public function show(int $id): Response
    {
        $svc = DichVu::with(['nhaCungCap.hoSoNhaCungCap', 'danhMuc.parent'])
            ->findOrFail($id);

        $hoso = $svc->nhaCungCap?->hoSoNhaCungCap;
        $reviewCount = DanhGia::where('nha_cung_cap_id', $svc->nha_cung_cap_id)->count();

        // Images
        $images = (is_array($svc->danh_sach_anh) && count($svc->danh_sach_anh) > 0)
            ? $svc->danh_sach_anh
            : [
                'https://picsum.photos/seed/' . md5($svc->id . 'a') . '/1200/800',
                'https://picsum.photos/seed/' . md5($svc->id . 'b') . '/600/400',
                'https://picsum.photos/seed/' . md5($svc->id . 'c') . '/600/400',
                'https://picsum.photos/seed/' . md5($svc->id . 'd') . '/600/400',
            ];

        // Giá hiển thị
        $priceText = number_format((float) $svc->gia_tu, 0, ',', '.') . 'đ';
        if ($svc->gia_den && $svc->gia_den > $svc->gia_tu) {
            $priceText .= ' - ' . number_format((float) $svc->gia_den, 0, ',', '.') . 'đ';
        }

        // Category info
        $parentCat = $svc->danhMuc?->parent ?? $svc->danhMuc;
        $subCat = $svc->danhMuc;

        // Attributes từ thuoc_tinh JSON hoặc fallback
        $attributes = [];
        if (is_array($svc->thuoc_tinh) && count($svc->thuoc_tinh) > 0) {
            foreach ($svc->thuoc_tinh as $key => $val) {
                $attributes[] = ['name' => $key, 'value' => $val];
            }
        }
        // Thêm đơn vị giá nếu có
        if ($svc->don_vi_gia) {
            $unit = trim(str_ireplace(['VND /', 'VND/'], '', $svc->don_vi_gia));
            $unit = trim(str_ireplace('VND', '', $unit));
            $unit = ltrim($unit, '/ ');
            $attributes[] = ['name' => 'Đơn vị', 'value' => $unit];
        }
        // Thêm khu vực phục vụ nếu có
        if (is_array($svc->khu_vuc_phuc_vu) && count($svc->khu_vuc_phuc_vu) > 0) {
            $attributes[] = ['name' => 'Khu vực phục vụ', 'value' => implode(', ', $svc->khu_vuc_phuc_vu)];
        }

        // Lịch làm việc
        $schedule = $svc->lich_lam_viec;

        // Recent reviews cho dịch vụ này (qua nha_cung_cap_id)
        $reviews = DanhGia::with('khachHang')
            ->where('nha_cung_cap_id', $svc->nha_cung_cap_id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                'name' => $r->an_danh ? 'Khách hàng ẩn danh' : $r->khachHang->ho_ten,
                'avatar' => $r->khachHang->anh_dai_dien ?? 'https://i.pravatar.cc/150?u=' . $r->khach_hang_id,
                'rating' => $r->so_sao,
                'content' => $r->noi_dung ?? '',
                'date' => $r->created_at?->format('d/m/Y'),
            ])->values()->all();

        $isFavorited = auth()->check() 
            ? YeuThich::where('nguoi_dung_id', auth()->id())->where('dich_vu_id', $svc->id)->exists() 
            : false;

        $service = [
            'id' => $svc->id,
            'title' => $svc->ten_dich_vu,
            'description' => $svc->mo_ta_chi_tiet ?? 'Chưa có mô tả chi tiết cho dịch vụ này.',
            'price' => (float) $svc->gia_tu,
            'priceTo' => $svc->gia_den ? (float) $svc->gia_den : null,
            'priceUnit' => (function() use ($svc) {
                $unit = trim(str_ireplace(['VND /', 'VND/'], '', $svc->don_vi_gia ?? 'lượt'));
                $unit = trim(str_ireplace('VND', '', $unit));
                return ltrim($unit, '/ ') ?: 'lượt';
            })(),
            'priceText' => $priceText,
            'rating' => (float) ($hoso?->diem_danh_gia ?? 0),
            'reviews' => $reviewCount,
            'location' => $svc->dia_chi_hien_thi ?? 'Đà Lạt',
            'images' => $images,
            'attributes' => $attributes,
            'schedule' => $schedule,
            'category' => [
                'name' => $parentCat?->ten_danh_muc ?? 'Dịch vụ',
                'slug' => $parentCat?->slug ?? '',
                'sub' => $subCat?->ten_danh_muc ?? '',
            ],
            'provider' => [
                'id' => $svc->nha_cung_cap_id,
                'name' => $hoso?->ten_thuong_hieu ?? $svc->nhaCungCap?->ho_ten ?? 'Nhà cung cấp',
                'rating' => (float) ($hoso?->diem_danh_gia ?? 0),
                'reviews' => $reviewCount,
                'verified' => true,
                'experience' => ($hoso?->nam_kinh_nghiem ?? 0) . ' năm',
                'avatar' => $svc->nhaCungCap?->anh_dai_dien ?? 'https://i.pravatar.cc/150?u=' . $svc->nha_cung_cap_id,
                'description' => $hoso?->gioi_thieu ?? '',
            ],
            'customerReviews' => $reviews,
            'is_favorited' => $isFavorited,
        ];

        return Inertia::render('services/Show', [
            'service' => $service,
        ]);
    }

    /**
     * AI Planner page — passes real recommended services.
     */
    public function aiPlanner(): Response
    {
        // Lấy top dịch vụ có đánh giá cao nhất
        $topServices = $this->serviceRepository->getActivePublicServices()
            ->sortByDesc(fn ($s) => $s->nhaCungCap?->hoSoNhaCungCap?->diem_danh_gia ?? 0)
            ->take(6)
            ->map(function ($svc) {
                $hoso = $svc->nhaCungCap?->hoSoNhaCungCap;
                $reviewCount = DanhGia::where('nha_cung_cap_id', $svc->nha_cung_cap_id)->count();
                $image = (is_array($svc->danh_sach_anh) && count($svc->danh_sach_anh) > 0)
                    ? $svc->danh_sach_anh[0]
                    : 'https://picsum.photos/seed/' . md5($svc->id) . '/400/250';

                return [
                    'id' => $svc->id,
                    'title' => $svc->ten_dich_vu,
                    'provider' => $hoso?->ten_thuong_hieu ?? 'Nhà cung cấp',
                    'rating' => (float) ($hoso?->diem_danh_gia ?? 0),
                    'reviews' => $reviewCount,
                    'price' => (float) $svc->gia_tu,
                    'image' => $image,
                ];
            })->values()->all();

        // Lấy locations có dịch vụ
        $locations = DichVu::where('trang_thai_hoat_dong', 'hoat_dong')
            ->whereNotNull('dia_chi_hien_thi')
            ->pluck('dia_chi_hien_thi')
            ->map(fn ($loc) => trim(last(explode("\n", $loc))))
            ->unique()
            ->filter()
            ->values()
            ->take(10)
            ->all();

        return Inertia::render('ai-planner/Index', [
            'recommendedServices' => $topServices,
            'availableLocations' => $locations,
        ]);
    }

    public function generateAiPlan(
        GenerateAiPlanRequest $request,
        GeminiItineraryPlannerService $plannerService
    ): JsonResponse {
        $result = $plannerService->generatePlan($request->validated(), $request->user());

        return response()->json($result);
    }

    public function chatAiPlanner(
        PlannerChatRequest $request,
        GeminiItineraryPlannerService $plannerService
    ): JsonResponse {
        $result = $plannerService->chat($request->validated(), $request->user());

        return response()->json($result);
    }
}
