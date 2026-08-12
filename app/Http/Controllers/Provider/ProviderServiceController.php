<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\ServiceStoreRequest;
use App\Http\Requests\Provider\ServiceUpdateRequest;
use App\Models\DanhGia;
use App\Models\DanhMucDichVu;
use App\Models\DichVu;
use App\Models\YeuThich;
use App\Repositories\Contracts\Service\ServiceRepositoryInterface;
use App\Services\Service\ServiceManagementService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class ProviderServiceController extends Controller
{
    public function __construct(
        protected ServiceManagementService $serviceManager,
        protected ServiceRepositoryInterface $serviceRepository
    ) {}
    /**
     * Danh sách dịch vụ của provider hiện tại.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->input('search');
        $status = $request->input('trang_thai');

        $services = $this->serviceRepository->getPaginatedForProvider($user->id, $status, $search, 10)
            ->withQueryString()
            ->through(function (DichVu $svc) {
                return [
                    'id' => $svc->id,
                    'ten_dich_vu' => $svc->ten_dich_vu,
                    'slug' => $svc->slug,
                    'danh_muc' => $svc->danhMuc?->ten_danh_muc ?? '—',
                    'gia_tu' => (float) $svc->gia_tu,
                    'gia_den' => (float) $svc->gia_den,
                    'don_vi_gia' => $svc->don_vi_gia,
                    'anh_dai_dien' => $svc->danh_sach_anh[0] ?? null,
                    'trang_thai_duyet' => $svc->trang_thai_duyet,
                    'trang_thai_hoat_dong' => $svc->trang_thai_hoat_dong,
                    'so_booking' => $svc->don_dat_lich_count,
                    'ngay_tao' => $svc->created_at?->format('d/m/Y'),
                ];
            });

        return Inertia::render('provider/Services', [
            'services' => $services,
            'filters' => $request->only(['search', 'trang_thai']),
        ]);
    }

    /**
     * Form tạo dịch vụ mới.
     */
    public function create()
    {
        $categories = DanhMucDichVu::whereNull('parent_id')
            ->with('children')
            ->where('trang_thai', 'hoat_dong')
            ->orderBy('thu_tu_hien_thi')
            ->get()
            ->map(fn ($cat) => [
                'id' => $cat->id,
                'ten_danh_muc' => $cat->ten_danh_muc,
                'children' => $cat->children->map(fn ($sub) => [
                    'id' => $sub->id,
                    'ten_danh_muc' => $sub->ten_danh_muc,
                ]),
            ]);

        return Inertia::render('provider/services/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Lưu dịch vụ mới vào DB.
     */
    public function store(ServiceStoreRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $images = [];
        if ($request->hasFile('anh_dich_vu')) {
            foreach ($request->file('anh_dich_vu') as $file) {
                $path = $file->store('services/' . $user->id, 'public');
                $images[] = '/storage/' . $path;
            }
        }
        
        $data['danh_sach_anh'] = !empty($images) ? $images : null;

        $this->serviceManager->createService($data, $user->id);

        return redirect()->route('provider.services')
            ->with('success', 'Dịch vụ đã được tạo thành công! Đang chờ duyệt.');
    }

    public function show(Request $request, int $id)
    {
        $svc = $this->serviceRepository->findByIdAndProvider($id, $request->user()->id);

        if (!$svc) {
            abort(404);
        }

        $svc->load(['nhaCungCap.hoSoNhaCungCap', 'danhMuc.parent']);

        $hoso = $svc->nhaCungCap?->hoSoNhaCungCap;
        $reviewCount = DanhGia::where('nha_cung_cap_id', $svc->nha_cung_cap_id)->count();

        $images = (is_array($svc->danh_sach_anh) && count($svc->danh_sach_anh) > 0)
            ? $svc->danh_sach_anh
            : [
                'https://picsum.photos/seed/' . md5($svc->id . 'a') . '/1200/800',
                'https://picsum.photos/seed/' . md5($svc->id . 'b') . '/600/400',
                'https://picsum.photos/seed/' . md5($svc->id . 'c') . '/600/400',
                'https://picsum.photos/seed/' . md5($svc->id . 'd') . '/600/400',
            ];

        $priceText = number_format((float) $svc->gia_tu, 0, ',', '.') . 'đ';
        if ($svc->gia_den && $svc->gia_den > $svc->gia_tu) {
            $priceText .= ' - ' . number_format((float) $svc->gia_den, 0, ',', '.') . 'đ';
        }

        $parentCat = $svc->danhMuc?->parent ?? $svc->danhMuc;
        $subCat = $svc->danhMuc;

        $attributes = [];
        if (is_array($svc->thuoc_tinh) && count($svc->thuoc_tinh) > 0) {
            foreach ($svc->thuoc_tinh as $key => $val) {
                $attributes[] = ['name' => $key, 'value' => $val];
            }
        }
        if ($svc->don_vi_gia) {
            $unit = trim(str_ireplace(['VND /', 'VND/'], '', $svc->don_vi_gia));
            $unit = trim(str_ireplace('VND', '', $unit));
            $unit = ltrim($unit, '/ ');
            $attributes[] = ['name' => 'Đơn vị', 'value' => $unit];
        }
        if (is_array($svc->khu_vuc_phuc_vu) && count($svc->khu_vuc_phuc_vu) > 0) {
            $attributes[] = ['name' => 'Khu vực phục vụ', 'value' => implode(', ', $svc->khu_vuc_phuc_vu)];
        }

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

        $isFavorited = YeuThich::where('nguoi_dung_id', $request->user()->id)
            ->where('dich_vu_id', $svc->id)
            ->exists();

        return Inertia::render('services/Show', [
            'service' => [
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
                'schedule' => $svc->lich_lam_viec,
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
            ],
        ]);
    }

    /**
     * Form sửa dịch vụ.
     */
    public function edit(Request $request, int $id)
    {
        $user = $request->user();
        $service = $this->serviceRepository->findByIdAndProvider($id, $user->id);
        
        if (!$service) {
            abort(404);
        }

        $categories = DanhMucDichVu::whereNull('parent_id')
            ->with('children')
            ->where('trang_thai', 'hoat_dong')
            ->orderBy('thu_tu_hien_thi')
            ->get()
            ->map(fn ($cat) => [
                'id' => $cat->id,
                'ten_danh_muc' => $cat->ten_danh_muc,
                'children' => $cat->children->map(fn ($sub) => [
                    'id' => $sub->id,
                    'ten_danh_muc' => $sub->ten_danh_muc,
                ]),
            ]);

        return Inertia::render('provider/services/Edit', [
            'service' => [
                'id' => $service->id,
                'ten_dich_vu' => $service->ten_dich_vu,
                'danh_muc_id' => $service->danh_muc_id,
                'mo_ta_chi_tiet' => $service->mo_ta_chi_tiet,
                'gia_tu' => (float) $service->gia_tu,
                'gia_den' => (float) $service->gia_den,
                'don_vi_gia' => $service->don_vi_gia,
                'thoi_luong_phut' => $service->thoi_luong_phut,
                'dia_chi_hien_thi' => $service->dia_chi_hien_thi,
                'danh_sach_anh' => $service->danh_sach_anh ?? [],
                'the_tu_khoa' => $service->the_tu_khoa ?? [],
                'khu_vuc_phuc_vu' => $service->khu_vuc_phuc_vu ?? [],
                'trang_thai_hoat_dong' => $service->trang_thai_hoat_dong,
                'trang_thai_duyet' => $service->trang_thai_duyet,
            ],
            'categories' => $categories,
        ]);
    }

    /**
     * Cập nhật dịch vụ.
     */
    public function update(ServiceUpdateRequest $request, int $id)
    {
        $user = $request->user();
        $data = $request->validated();

        $newImages = [];
        if ($request->hasFile('anh_dich_vu')) {
            foreach ($request->file('anh_dich_vu') as $file) {
                $path = $file->store('services/' . $user->id, 'public');
                $newImages[] = '/storage/' . $path;
            }
        }

        $imagesToRemove = $request->input('anh_xoa', []);

        try {
            $this->serviceManager->updateService($id, $user->id, $data, $newImages, $imagesToRemove);
            return redirect()->route('provider.services')
                ->with('success', 'Dịch vụ đã được cập nhật thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Xóa dịch vụ (ẩn).
     */
    public function destroy(Request $request, int $id)
    {
        try {
            $this->serviceManager->deleteService($id, $request->user()->id);
            return redirect()->route('provider.services')
                ->with('success', 'Dịch vụ đã được xóa.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bật/tắt trạng thái hoạt động dịch vụ.
     */
    public function toggleStatus(Request $request, int $id)
    {
        try {
            $newStatus = $this->serviceManager->toggleServiceStatus($id, $request->user()->id);
            $message = $newStatus === 'hoat_dong'
                ? 'Dịch vụ đã được kích hoạt lại.'
                : 'Dịch vụ đã tạm ngưng.';
            return back()->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
