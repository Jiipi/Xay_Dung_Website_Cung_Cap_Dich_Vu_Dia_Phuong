<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiaoDich;
use App\Models\HoSoNhaCungCap;
use App\Models\ThongBao;
use App\Models\YeuCauRutTien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminFinanceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query = YeuCauRutTien::with('nguoiDung.hoSoNhaCungCap')->latest();

        if ($status && $status !== 'all') {
            $query->where('trang_thai', $status);
        }

        $requests = $query->paginate(15)->through(
            fn($withdrawal) => [
                'id' => $withdrawal->id,
                'provider' => $withdrawal->nguoiDung?->ho_ten ?? '—',
                'so_tien' => (float) $withdrawal->so_tien,
                'trang_thai' => $withdrawal->trang_thai,
                'ngay_tao' => $withdrawal->created_at?->format('H:i d/m/Y'),
                'ghi_chu' => $withdrawal->ghi_chu,
                'admin_ghi_chu' => $withdrawal->admin_ghi_chu,
                'thong_tin_ngan_hang' => $withdrawal->nguoiDung?->hoSoNhaCungCap
                    ? [
                        'ten_ngan_hang' =>
                            $withdrawal->nguoiDung->hoSoNhaCungCap
                                ->ten_ngan_hang,
                        'stk_ngan_hang' =>
                            $withdrawal->nguoiDung->hoSoNhaCungCap
                                ->stk_ngan_hang,
                        'ten_chu_tk' =>
                            $withdrawal->nguoiDung->hoSoNhaCungCap->ten_chu_tk,
                    ]
                    : null,
            ],
        );

        return Inertia::render('admin/Finance/Index', [
            'requests' => $requests,
            'stats' => [
                'pending' => YeuCauRutTien::where(
                    'trang_thai',
                    'cho_xu_ly',
                )->count(),
                'approved' => YeuCauRutTien::where(
                    'trang_thai',
                    'da_duyet',
                )->count(),
                'total_platform_fee' => GiaoDich::where(
                    'loai_giao_dich',
                    'thu_phi_nen_tang',
                )->sum('so_tien'),
            ],
            'filters' => $request->only('status'),
        ]);
    }

    public function approve(Request $request, int $id)
    {
        $withdrawal = DB::transaction(function () use ($id) {
            $withdrawal = YeuCauRutTien::query()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($withdrawal->trang_thai !== 'cho_xu_ly') {
                return null;
            }

            $withdrawal->update([
                'trang_thai' => 'da_duyet',
                'admin_ghi_chu' => 'Đã chuyển khoản thành công',
            ]);

            $this->withdrawalTransaction($withdrawal)?->update([
                'trang_thai' => 'thanh_cong',
            ]);

            return $withdrawal;
        });

        if (!$withdrawal) {
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        ThongBao::create([
            'nguoi_dung_id' => $withdrawal->nguoi_dung_id,
            'tieu_de' => 'Yêu cầu rút tiền đã được duyệt',
            'noi_dung' =>
                'Yêu cầu rút ' .
                number_format($withdrawal->so_tien) .
                ' VNĐ đã được chuyển khoản. Vui lòng kiểm tra tài khoản ngân hàng.',
            'loai_thong_bao' => 'finance_approved',
        ]);

        return back()->with('success', 'Đã duyệt yêu cầu rút tiền.');
    }

    public function reject(Request $request, int $id)
    {
        $validated = $request->validate([
            'ly_do' => 'required|string|max:1000',
        ]);

        $withdrawal = DB::transaction(function () use ($id, $validated) {
            $withdrawal = YeuCauRutTien::query()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($withdrawal->trang_thai !== 'cho_xu_ly') {
                return null;
            }

            $profile = HoSoNhaCungCap::query()
                ->lockForUpdate()
                ->find($withdrawal->nguoi_dung_id);

            if ($profile) {
                $profile->so_du =
                    (float) $profile->so_du + (float) $withdrawal->so_tien;
                $profile->save();
            }

            $withdrawal->update([
                'trang_thai' => 'tu_choi',
                'admin_ghi_chu' => $validated['ly_do'],
            ]);

            $this->withdrawalTransaction($withdrawal)?->update([
                'trang_thai' => 'that_bai',
            ]);

            return $withdrawal;
        });

        if (!$withdrawal) {
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        ThongBao::create([
            'nguoi_dung_id' => $withdrawal->nguoi_dung_id,
            'tieu_de' => 'Yêu cầu rút tiền bị từ chối',
            'noi_dung' =>
                'Yêu cầu rút ' .
                number_format($withdrawal->so_tien) .
                ' VNĐ đã bị từ chối. Lý do: ' .
                $validated['ly_do'],
            'loai_thong_bao' => 'finance_rejected',
        ]);

        return back()->with(
            'success',
            'Đã từ chối yêu cầu và hoàn tiền vào ví.',
        );
    }

    private function withdrawalTransaction(YeuCauRutTien $withdrawal): ?GiaoDich
    {
        if ($withdrawal->giao_dich_id) {
            return GiaoDich::query()
                ->lockForUpdate()
                ->find($withdrawal->giao_dich_id);
        }

        return GiaoDich::query()
            ->where('nguoi_dung_id', $withdrawal->nguoi_dung_id)
            ->where('loai_giao_dich', 'rut_tien')
            ->where('so_tien', $withdrawal->so_tien)
            ->where('trang_thai', 'cho_xu_ly')
            ->latest()
            ->lockForUpdate()
            ->first();
    }
}
