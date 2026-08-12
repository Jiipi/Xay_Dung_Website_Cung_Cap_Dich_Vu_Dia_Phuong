<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\DichVu;
use App\Models\YeuThich;
use App\Repositories\Contracts\Service\ServiceRepositoryInterface;
use App\Services\Service\ServiceManagementService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class AdminServiceController extends Controller
{
    public function __construct(
        protected ServiceManagementService $serviceManager,
        protected ServiceRepositoryInterface $serviceRepository
    ) {}
    public function index(Request $request)
    {
        $status = $request->filled('status') ? $request->status : null;
        $search = $request->filled('search') ? $request->search : null;

        $services = $this->serviceRepository->getPaginatedForAdmin($status, $search, 15)
            ->through(fn ($sv) => [
                'id' => $sv->id,
                'ten_dich_vu' => $sv->ten_dich_vu,
                'hinh_anh' => $sv->danh_sach_anh[0] ?? null,
                'gia_tien' => (float) ($sv->gia_tu ?? 0),
                'don_vi' => $sv->don_vi_gia,
                'nha_cung_cap' => $sv->nhaCungCap?->ho_ten ?? '—',
                'danh_muc' => $sv->danhMuc?->ten_danh_muc ?? '—',
                'trang_thai_duyet' => $sv->trang_thai_duyet,
                'trang_thai' => $sv->trang_thai_hoat_dong,
                'ngay_tao' => $sv->created_at?->format('d/m/Y'),
            ]);

        $statusCounts = $this->serviceRepository->getStatusCountsForAdmin();

        return Inertia::render('admin/Services', [
            'services' => $services,
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $svc = DichVu::with(['nhaCungCap.hoSoNhaCungCap', 'danhMuc.parent'])->findOrFail($id);
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
            $attributes[] = ['name' => 'Đơn vị', 'value' => ltrim($unit, '/ ')];
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
                'is_favorited' => YeuThich::where('nguoi_dung_id', $request->user()->id)->where('dich_vu_id', $svc->id)->exists(),
            ],
        ]);
    }

    public function approve($id)
    {
        try {
            $this->serviceManager->approveService($id);
            return back()->with('success', "Đã duyệt dịch vụ");
        } catch (Exception $e) {
            return back()->with('error', "Có lỗi xảy ra");
        }
    }

    public function reject($id)
    {
        try {
            $this->serviceManager->rejectService($id);
            return back()->with('success', "Đã từ chối dịch vụ");
        } catch (Exception $e) {
            return back()->with('error', "Có lỗi xảy ra");
        }
    }
}
