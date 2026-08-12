<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\GiaoDich;
use App\Models\HoSoNhaCungCap;
use App\Models\YeuCauRutTien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ProviderFinanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->hoSoNhaCungCap;

        $transactions = GiaoDich::where('nguoi_dung_id', $user->id)
            ->latest()
            ->paginate(10)
            ->through(
                fn($transaction) => [
                    'id' => $transaction->id,
                    'loai_giao_dich' => $transaction->loai_giao_dich,
                    'so_tien' => (float) $transaction->so_tien,
                    'trang_thai' => $transaction->trang_thai,
                    'ghi_chu' => $transaction->ghi_chu,
                    'ngay_tao' => $transaction->created_at?->format(
                        'H:i d/m/Y',
                    ),
                ],
            );

        $withdrawals = YeuCauRutTien::where('nguoi_dung_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(
                fn($withdrawal) => [
                    'id' => $withdrawal->id,
                    'so_tien' => (float) $withdrawal->so_tien,
                    'trang_thai' => $withdrawal->trang_thai,
                    'admin_ghi_chu' => $withdrawal->admin_ghi_chu,
                    'ngay_tao' => $withdrawal->created_at?->format('H:i d/m/Y'),
                ],
            );

        return Inertia::render('provider/Finance/Index', [
            'so_du' => (float) ($profile?->so_du ?? 0),
            'giaoDich' => $transactions,
            'yeuCauRutTien' => $withdrawals,
            'stk_ngan_hang' => $profile?->stk_ngan_hang,
            'ten_ngan_hang' => $profile?->ten_ngan_hang,
        ]);
    }

    public function requestWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'so_tien' => 'required|numeric|min:50000',
        ]);

        $amount = (float) $validated['so_tien'];
        $userId = (int) auth()->id();

        DB::transaction(function () use ($amount, $userId): void {
            $profile = HoSoNhaCungCap::query()->lockForUpdate()->find($userId);

            if (!$profile || (float) $profile->so_du < $amount) {
                throw ValidationException::withMessages([
                    'so_tien' => 'Số dư không đủ để thực hiện rút tiền.',
                ]);
            }

            if (!$profile->stk_ngan_hang || !$profile->ten_ngan_hang) {
                throw ValidationException::withMessages([
                    'so_tien' =>
                        'Vui lòng cập nhật thông tin ngân hàng trong Hồ sơ trước khi rút tiền.',
                ]);
            }

            $transaction = GiaoDich::create([
                'nguoi_dung_id' => $userId,
                'loai_giao_dich' => 'rut_tien',
                'so_tien' => $amount,
                'phuong_thuc' => 'ngan_hang',
                'trang_thai' => 'cho_xu_ly',
                'ghi_chu' => 'Tạm trừ số dư tạo yêu cầu rút tiền',
            ]);

            YeuCauRutTien::create([
                'nguoi_dung_id' => $userId,
                'giao_dich_id' => $transaction->id,
                'so_tien' => $amount,
                'trang_thai' => 'cho_xu_ly',
                'ghi_chu' => "Rút tiền về {$profile->ten_ngan_hang} - {$profile->stk_ngan_hang}",
            ]);

            $profile->so_du = (float) $profile->so_du - $amount;
            $profile->save();
        });

        return back()->with(
            'success',
            'Đã gửi yêu cầu rút tiền thành công. Vui lòng chờ Admin xử lý.',
        );
    }
}
