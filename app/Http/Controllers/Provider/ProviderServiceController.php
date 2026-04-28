<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\ServiceStoreRequest;
use App\Http\Requests\Provider\ServiceUpdateRequest;
use App\Models\DanhMucDichVu;
use App\Models\DichVu;
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
