<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DichVu;
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
