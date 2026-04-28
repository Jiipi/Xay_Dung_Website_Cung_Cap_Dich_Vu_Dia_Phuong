<?php

namespace App\Services\Booking;

use App\Models\DichVu;
use App\Models\ThongBao;
use App\Repositories\Contracts\Booking\BookingRepositoryInterface;
use Carbon\Carbon;
use Exception;

class BookingService
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository
    ) {}

    public function createBooking(array $data, int $customerId): array
    {
        $dichVu = DichVu::with('nhaCungCap')->findOrFail($data['dich_vu_id']);

        // Parse date + time
        $scheduledAt = Carbon::parse($data['thoi_gian_thuc_hien']);
        if (!empty($data['khung_gio'])) {
            $hour = (int) explode(':', $data['khung_gio'])[0];
            $scheduledAt->setHour($hour)->setMinute(0);
        }

        // Calculate price
        $soLuong = (float) $data['so_luong'];
        $giaVon  = (float) ($dichVu->gia_tu ?? 0);
        $tamTinh  = $giaVon * $soLuong;
        $phiDv    = 0;
        $giamGia  = 0;
        $tongTien = $tamTinh + $phiDv - $giamGia;

        // Generate booking code
        $maDon = 'DL-' . strtoupper(substr(md5(uniqid()), 0, 8));

        $booking = $this->bookingRepository->create([
            'ma_don'                 => $maDon,
            'khach_hang_id'          => $customerId,
            'nha_cung_cap_id'        => $dichVu->nha_cung_cap_id,
            'dich_vu_id'             => $dichVu->id,
            'thoi_gian_thuc_hien'    => $scheduledAt,
            'so_luong'               => $soLuong,
            'don_vi'                 => $dichVu->don_vi_gia ?? 'lượt',
            'dia_diem_thuc_hien'     => $data['dia_diem_thuc_hien'] ?? $dichVu->dia_chi_hien_thi,
            'ghi_chu'                => $data['ghi_chu'] ?? null,
            'tam_tinh'               => $tamTinh,
            'phi_dich_vu'            => $phiDv,
            'giam_gia'               => $giamGia,
            'tong_tien'              => $tongTien,
            'trang_thai_don'         => 'cho_xac_nhan',
            'phuong_thuc_thanh_toan' => 'cod',
            'trang_thai_thanh_toan'  => 'cho_thanh_toan',
        ]);

        $this->notifyCustomer($customerId, 'Đặt lịch thành công', "Đơn {$maDon} — {$dichVu->ten_dich_vu} đã được gửi. Chờ nhà cung cấp xác nhận.", 'booking_created');
        
        $customerName = \App\Models\User::find($customerId)?->ho_ten ?? 'Khách hàng';
        $this->notifyProvider($dichVu->nha_cung_cap_id, 'Có đơn đặt lịch mới', "{$customerName} đã đặt {$dichVu->ten_dich_vu} vào {$scheduledAt->format('d/m/Y H:i')}.", 'booking_new');

        return [
            'success' => true,
            'booking_id' => $booking->id,
        ];
    }

    public function customerCancelBooking(int $id, int $customerId, string $reason): bool
    {
        $booking = $this->bookingRepository->findByIdAndCustomer($id, $customerId);
        
        if (!$booking || !in_array($booking->trang_thai_don, ['cho_xac_nhan', 'da_xac_nhan'])) {
            throw new Exception("Không thể hủy đơn hàng này.");
        }

        $this->bookingRepository->update($id, [
            'trang_thai_don' => 'da_huy',
            'huy_boi'        => 'customer',
            'ly_do_huy'      => $reason,
        ]);

        $this->notifyProvider($booking->nha_cung_cap_id, 'Đơn đặt lịch đã bị hủy', "Đơn {$booking->ma_don} đã được khách hàng hủy.", 'booking_cancelled');

        return true;
    }

    public function providerConfirmBooking(int $id, int $providerId): bool
    {
        $booking = $this->bookingRepository->findByIdAndProvider($id, $providerId);

        if (!$booking || $booking->trang_thai_don !== 'cho_xac_nhan') {
            throw new Exception("Đơn hàng không hợp lệ để xác nhận.");
        }

        $this->bookingRepository->update($id, ['trang_thai_don' => 'da_xac_nhan']);

        $this->notifyCustomer($booking->khach_hang_id, 'Booking đã được xác nhận', "Đơn {$booking->ma_don} đã được nhà cung cấp xác nhận.", 'booking_confirmed');

        return true;
    }

    public function providerRejectBooking(int $id, int $providerId, string $reason): bool
    {
        $booking = $this->bookingRepository->findByIdAndProvider($id, $providerId);

        if (!$booking || $booking->trang_thai_don !== 'cho_xac_nhan') {
            throw new Exception("Đơn hàng không hợp lệ để từ chối.");
        }

        $this->bookingRepository->update($id, [
            'trang_thai_don' => 'da_huy',
            'huy_boi'        => 'nha_cung_cap',
            'ly_do_huy'      => $reason,
        ]);

        $this->notifyCustomer($booking->khach_hang_id, 'Booking đã bị từ chối', "Đơn {$booking->ma_don} đã bị nhà cung cấp từ chối. Lý do: {$reason}", 'booking_rejected');

        return true;
    }

    public function providerCompleteBooking(int $id, int $providerId): bool
    {
        $booking = $this->bookingRepository->findByIdAndProvider($id, $providerId);

        if (!$booking || $booking->trang_thai_don !== 'da_xac_nhan') {
            throw new Exception("Đơn hàng chưa được xác nhận, không thể hoàn thành.");
        }

        $this->bookingRepository->update($id, [
            'trang_thai_don' => 'hoan_thanh',
            'trang_thai_thanh_toan' => 'da_thanh_toan',
        ]);

        $this->notifyCustomer($booking->khach_hang_id, 'Dịch vụ đã hoàn thành', "Đơn {$booking->ma_don} đã hoàn thành. Hãy đánh giá dịch vụ!", 'booking_completed');

        return true;
    }

    public function adminForceConfirm(int $id): bool
    {
        $booking = $this->bookingRepository->findById($id);
        if (!$booking) return false;

        $this->bookingRepository->update($id, ['trang_thai_don' => 'da_xac_nhan']);

        $this->notifyCustomer($booking->khach_hang_id, 'Booking đã được xác nhận', "Đơn {$booking->ma_don} đã được Admin xác nhận.", 'booking_confirmed');
        $this->notifyProvider($booking->nha_cung_cap_id, 'Admin đã xác nhận booking', "Đơn {$booking->ma_don} đã được Admin xác nhận thay bạn.", 'booking_confirmed');

        return true;
    }

    public function adminForceComplete(int $id): bool
    {
        $booking = $this->bookingRepository->findById($id);
        if (!$booking) return false;

        $this->bookingRepository->update($id, [
            'trang_thai_don' => 'hoan_thanh',
            'trang_thai_thanh_toan' => 'da_thanh_toan',
        ]);

        $this->notifyCustomer($booking->khach_hang_id, 'Dịch vụ đã hoàn thành', "Đơn {$booking->ma_don} đã được Admin đánh dấu hoàn thành.", 'booking_completed');
        $this->notifyProvider($booking->nha_cung_cap_id, 'Dịch vụ đã hoàn thành', "Đơn {$booking->ma_don} đã được Admin đánh dấu hoàn thành.", 'booking_completed');

        return true;
    }

    public function adminForceReject(int $id): bool
    {
        $booking = $this->bookingRepository->findById($id);
        if (!$booking) return false;

        $this->bookingRepository->update($id, [
            'trang_thai_don' => 'da_huy',
            'huy_boi'        => 'admin',
            'ly_do_huy'      => 'Bị hủy bởi Quản trị viên (Admin)',
        ]);

        $this->notifyCustomer($booking->khach_hang_id, 'Booking đã bị hủy', "Đơn {$booking->ma_don} đã bị Admin hệ thống hủy bỏ.", 'booking_cancelled');
        $this->notifyProvider($booking->nha_cung_cap_id, 'Booking đã bị hủy', "Đơn {$booking->ma_don} đã bị Admin hệ thống hủy bỏ.", 'booking_cancelled');

        return true;
    }

    // Temporary helper until NotificationService is fully extracted
    private function notifyCustomer(int $userId, string $title, string $body, string $type)
    {
        ThongBao::create([
            'nguoi_dung_id' => $userId,
            'tieu_de'       => $title,
            'noi_dung'      => $body,
            'loai_thong_bao'=> $type,
            'da_doc'        => false,
        ]);
    }

    private function notifyProvider(int $userId, string $title, string $body, string $type)
    {
        ThongBao::create([
            'nguoi_dung_id' => $userId,
            'tieu_de'       => $title,
            'noi_dung'      => $body,
            'loai_thong_bao'=> $type,
            'da_doc'        => false,
        ]);
    }
}
