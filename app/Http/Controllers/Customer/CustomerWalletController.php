<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\GiaoDich;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CustomerWalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $transactions = GiaoDich::where('nguoi_dung_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(
                fn($transaction) => [
                    'id' => $transaction->id,
                    'loai' => $transaction->loai_giao_dich,
                    'so_tien' => (float) $transaction->so_tien,
                    'phuong_thuc' => $transaction->phuong_thuc,
                    'trang_thai' => $transaction->trang_thai,
                    'ghi_chu' => $transaction->ghi_chu,
                    'ma_giao_dich' => $transaction->ma_giao_dich_doi_tac,
                    'ngay_tao' => $transaction->created_at?->format(
                        'd/m/Y H:i',
                    ),
                ],
            );

        return Inertia::render('customer/Wallet/Index', [
            'so_du' => (float) ($user->so_du ?? 0),
            'transactions' => $transactions,
            'demo_topup_enabled' => (bool) config('payments.demo_enabled'),
        ]);
    }

    public function topup(Request $request)
    {
        abort_unless(
            config('payments.demo_enabled'),
            403,
            'Nạp tiền mô phỏng chỉ được bật trong môi trường demo.',
        );

        $validated = $request->validate([
            'so_tien' => 'required|numeric|min:10000|max:10000000',
        ]);

        $amount = (float) $validated['so_tien'];

        DB::transaction(function () use ($amount): void {
            $user = User::query()->lockForUpdate()->findOrFail(auth()->id());

            $user->so_du = (float) ($user->so_du ?? 0) + $amount;
            $user->save();

            GiaoDich::create([
                'nguoi_dung_id' => $user->id,
                'don_dat_lich_id' => null,
                'loai_giao_dich' => 'nap_tien',
                'so_tien' => $amount,
                'phuong_thuc' => 'vi_noi_bo',
                'ma_giao_dich_doi_tac' => 'SIM' . strtoupper(Str::random(10)),
                'trang_thai' => 'thanh_cong',
                'ghi_chu' => 'Nạp tiền vào ví (Mô phỏng)',
            ]);
        });

        return redirect()
            ->route('customer.wallet')
            ->with(
                'success',
                'Nạp ' . number_format($amount) . ' VNĐ thành công!',
            );
    }
}
