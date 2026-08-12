<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CauHinh;
use App\Models\DonDatLich;
use App\Models\GiaoDich;
use App\Models\ThongBao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function checkout(Request $request, int $bookingId)
    {
        $booking = DonDatLich::with('dichVu')->findOrFail($bookingId);

        abort_unless($booking->khach_hang_id === auth()->id(), 403);

        if (
            !in_array(
                $booking->trang_thai_don,
                ['cho_xac_nhan', 'da_xac_nhan'],
                true,
            )
        ) {
            return redirect()
                ->route('customer.bookings.show', $booking->id)
                ->with(
                    'error',
                    'Đơn đặt lịch không ở trạng thái có thể thanh toán.',
                );
        }

        if (
            in_array(
                $booking->trang_thai_thanh_toan,
                ['da_thanh_toan', 'da_dat_coc'],
                true,
            )
        ) {
            return redirect()
                ->route('customer.bookings.show', $booking->id)
                ->with('error', 'Đơn này đã được thanh toán hoặc đặt cọc.');
        }

        $deposit = round((float) $booking->tong_tien * 0.3, 2);
        $booking->update(['tien_coc' => $deposit]);

        return Inertia::render('customer/payment/Checkout', [
            'booking' => [
                'id' => $booking->id,
                'code' => $booking->ma_don,
                'total' => (float) $booking->tong_tien,
                'deposit' => $deposit,
                'service' => $booking->dichVu?->ten_dich_vu,
            ],
            'wallet_balance' => (float) (auth()->user()->so_du ?? 0),
            'demo_payment_enabled' => (bool) config('payments.demo_enabled'),
        ]);
    }

    public function process(Request $request, int $bookingId)
    {
        $validated = $request->validate([
            'action' => 'required|in:wallet,success,fail',
        ]);

        $action = $validated['action'];

        if ($action === 'fail') {
            return redirect()
                ->route('customer.bookings.show', $bookingId)
                ->with('error', 'Thanh toán bị hủy hoặc thất bại.');
        }

        if ($action === 'success' && !config('payments.demo_enabled')) {
            return redirect()
                ->route('customer.payment.checkout', $bookingId)
                ->with(
                    'error',
                    'Thanh toán mô phỏng đã bị tắt trên môi trường này.',
                );
        }

        return DB::transaction(function () use ($action, $bookingId) {
            $booking = DonDatLich::query()
                ->lockForUpdate()
                ->findOrFail($bookingId);

            abort_unless($booking->khach_hang_id === auth()->id(), 403);

            if (
                !in_array(
                    $booking->trang_thai_don,
                    ['cho_xac_nhan', 'da_xac_nhan'],
                    true,
                )
            ) {
                return redirect()
                    ->route('customer.bookings.show', $booking->id)
                    ->with(
                        'error',
                        'Đơn đặt lịch không ở trạng thái có thể thanh toán.',
                    );
            }

            if (
                in_array(
                    $booking->trang_thai_thanh_toan,
                    ['da_thanh_toan', 'da_dat_coc'],
                    true,
                )
            ) {
                return redirect()
                    ->route('customer.bookings.show', $booking->id)
                    ->with('error', 'Đơn này đã được thanh toán hoặc đặt cọc.');
            }

            $deposit = round((float) $booking->tong_tien * 0.3, 2);
            $feePercent =
                (float) (CauHinh::where('key', 'platform_fee_percent')->value(
                    'value',
                ) ?? 10);
            $platformFee = round(
                (float) $booking->tong_tien * ($feePercent / 100),
                2,
            );
            $transactionCode =
                ($action === 'wallet' ? 'WAL' : 'VNP') .
                strtoupper(Str::random(10));
            $paymentMethod = $action === 'wallet' ? 'vi_noi_bo' : 'vnpay';

            if ($action === 'wallet') {
                $user = User::query()
                    ->lockForUpdate()
                    ->findOrFail(auth()->id());

                if ((float) ($user->so_du ?? 0) < $deposit) {
                    return redirect()
                        ->route('customer.payment.checkout', $booking->id)
                        ->with(
                            'error',
                            'Số dư ví không đủ. Vui lòng nạp thêm tiền.',
                        );
                }

                $user->so_du = (float) $user->so_du - $deposit;
                $user->save();
            }

            $booking->update([
                'tien_coc' => $deposit,
                'trang_thai_thanh_toan' => 'da_dat_coc',
                'phuong_thuc_thanh_toan' => $paymentMethod,
                'ma_giao_dich_doi_tac' => $transactionCode,
                'phi_nen_tang' => $platformFee,
            ]);

            GiaoDich::create([
                'nguoi_dung_id' => auth()->id(),
                'don_dat_lich_id' => $booking->id,
                'loai_giao_dich' =>
                    $action === 'wallet' ? 'thanh_toan_vi' : 'thanh_toan_coc',
                'so_tien' => $deposit,
                'phuong_thuc' => $paymentMethod,
                'ma_giao_dich_doi_tac' => $transactionCode,
                'trang_thai' => 'thanh_cong',
                'ghi_chu' =>
                    $action === 'wallet'
                        ? 'Đặt cọc từ ví nội bộ'
                        : 'Đặt cọc qua VNPay (Mô phỏng)',
            ]);

            ThongBao::create([
                'nguoi_dung_id' => $booking->nha_cung_cap_id,
                'tieu_de' => 'Khách hàng đã đặt cọc',
                'noi_dung' =>
                    "Đơn {$booking->ma_don} đã được đặt cọc " .
                    number_format($deposit) .
                    ' VNĐ.',
                'loai_thong_bao' => 'payment_success',
                'da_doc' => false,
            ]);

            return redirect()
                ->route('customer.bookings.show', $booking->id)
                ->with('success', 'Đặt cọc thành công!');
        });
    }
}
