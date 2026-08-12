<?php

namespace App\Services\Booking;

use App\Events\BookingUpdated;
use App\Models\DichVu;
use App\Models\DonDatLich;
use App\Models\User;
use App\Repositories\Contracts\Booking\BookingRepositoryInterface;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository,
        protected NotificationService $notificationService,
    ) {}

    public function createBooking(array $data, int $customerId): array
    {
        return DB::transaction(function () use ($data, $customerId): array {
            $dichVu = DichVu::with('nhaCungCap')
                ->where('trang_thai_duyet', 'da_duyet')
                ->where('trang_thai_hoat_dong', 'hoat_dong')
                ->findOrFail($data['dich_vu_id']);

            // Serialize availability checks for this provider so concurrent requests
            // cannot both pass the overlap check and create a double booking.
            User::query()
                ->lockForUpdate()
                ->findOrFail($dichVu->nha_cung_cap_id);

            // Parse date + time
            $scheduledAt = Carbon::parse($data['thoi_gian_thuc_hien']);
            if (!empty($data['khung_gio'])) {
                $hour = (int) explode(':', $data['khung_gio'])[0];
                $scheduledAt->setHour($hour)->setMinute(0);
            }

            // Calculate End Time
            $thoiLuong = $dichVu->thoi_luong_phut ?? 120;
            $scheduledEnd = (clone $scheduledAt)->addMinutes($thoiLuong);

            // Check for Double Booking Overlap
            $existingBookings = DonDatLich::with('dichVu')
                ->where('nha_cung_cap_id', $dichVu->nha_cung_cap_id)
                ->whereIn('trang_thai_don', [
                    'cho_xac_nhan',
                    'da_xac_nhan',
                    'dang_thuc_hien',
                ])
                ->whereDate('thoi_gian_thuc_hien', $scheduledAt->toDateString())
                ->get();

            foreach ($existingBookings as $eb) {
                $ebStart = Carbon::parse($eb->thoi_gian_thuc_hien);
                $ebDuration = $eb->dichVu?->thoi_luong_phut ?? 120;
                $ebEnd = (clone $ebStart)->addMinutes($ebDuration);

                // Overlap condition: StartA < EndB AND EndA > StartB
                if ($scheduledAt->lt($ebEnd) && $scheduledEnd->gt($ebStart)) {
                    throw new Exception(
                        "Nhà cung cấp đã có lịch bận từ {$ebStart->format(
                            'H:i',
                        )} đến {$ebEnd->format(
                            'H:i',
                        )}. Vui lòng chọn giờ khác.",
                    );
                }
            }

            // Calculate price
            $soLuong = (float) $data['so_luong'];
            $giaVon = (float) ($dichVu->gia_tu ?? 0);
            $tamTinh = $giaVon * $soLuong;
            $phiDv = 0;
            $giamGia = 0;
            $tongTien = $tamTinh + $phiDv - $giamGia;

            // Generate booking code
            $maDon = 'DL-' . strtoupper(substr(md5(uniqid()), 0, 8));

            $booking = $this->bookingRepository->create([
                'ma_don' => $maDon,
                'khach_hang_id' => $customerId,
                'nha_cung_cap_id' => $dichVu->nha_cung_cap_id,
                'dich_vu_id' => $dichVu->id,
                'thoi_gian_thuc_hien' => $scheduledAt,
                'so_luong' => $soLuong,
                'don_vi' => $dichVu->don_vi_gia ?? 'lượt',
                'dia_diem_thuc_hien' =>
                    $data['dia_diem_thuc_hien'] ?? $dichVu->dia_chi_hien_thi,
                'ghi_chu' => $data['ghi_chu'] ?? null,
                'tam_tinh' => $tamTinh,
                'phi_dich_vu' => $phiDv,
                'giam_gia' => $giamGia,
                'tong_tien' => $tongTien,
                'trang_thai_don' => 'cho_xac_nhan',
                'phuong_thuc_thanh_toan' => 'cod',
                'trang_thai_thanh_toan' => 'cho_thanh_toan',
            ]);

            $this->notifyCustomer(
                $customerId,
                'Đặt lịch thành công',
                "Đơn {$maDon} — {$dichVu->ten_dich_vu} đã được gửi. Chờ nhà cung cấp xác nhận.",
                'booking_created',
            );

            $customerName =
                \App\Models\User::find($customerId)?->ho_ten ?? 'Khách hàng';
            $this->notifyProvider(
                $dichVu->nha_cung_cap_id,
                'Có đơn đặt lịch mới',
                "{$customerName} đã đặt {$dichVu->ten_dich_vu} vào {$scheduledAt->format(
                    'd/m/Y H:i',
                )}.",
                'booking_new',
            );
            $this->broadcastBooking($booking);

            return [
                'success' => true,
                'booking_id' => $booking->id,
            ];
        });
    }

    public function customerCancelBooking(
        int $id,
        int $customerId,
        string $reason,
    ): bool {
        return DB::transaction(function () use (
            $id,
            $customerId,
            $reason,
        ): bool {
            $booking = DonDatLich::query()
                ->where('khach_hang_id', $customerId)
                ->lockForUpdate()
                ->find($id);

            if (
                !$booking ||
                !in_array($booking->trang_thai_don, [
                    'cho_xac_nhan',
                    'da_xac_nhan',
                ])
            ) {
                throw new Exception('Không thể hủy đơn hàng này.');
            }

            $this->bookingRepository->update($id, [
                'trang_thai_don' => 'da_huy',
                'huy_boi' => 'customer',
                'ly_do_huy' => $reason,
            ]);
            $this->broadcastBooking($booking->fresh());

            $this->notifyProvider(
                $booking->nha_cung_cap_id,
                'Đơn đặt lịch đã bị hủy',
                "Đơn {$booking->ma_don} đã được khách hàng hủy.",
                'booking_cancelled',
            );

            return true;
        });
    }

    public function providerConfirmBooking(int $id, int $providerId): bool
    {
        return DB::transaction(function () use ($id, $providerId): bool {
            $booking = DonDatLich::query()
                ->where('nha_cung_cap_id', $providerId)
                ->lockForUpdate()
                ->find($id);

            if (!$booking || $booking->trang_thai_don !== 'cho_xac_nhan') {
                throw new Exception('Đơn hàng không hợp lệ để xác nhận.');
            }

            $this->bookingRepository->update($id, [
                'trang_thai_don' => 'da_xac_nhan',
            ]);
            $this->broadcastBooking($booking->fresh());

            $this->notifyCustomer(
                $booking->khach_hang_id,
                'Booking đã được xác nhận',
                "Đơn {$booking->ma_don} đã được nhà cung cấp xác nhận.",
                'booking_confirmed',
            );

            return true;
        });
    }

    public function providerRejectBooking(
        int $id,
        int $providerId,
        string $reason,
    ): bool {
        return DB::transaction(function () use (
            $id,
            $providerId,
            $reason,
        ): bool {
            $booking = DonDatLich::query()
                ->where('nha_cung_cap_id', $providerId)
                ->lockForUpdate()
                ->find($id);

            if (!$booking || $booking->trang_thai_don !== 'cho_xac_nhan') {
                throw new Exception('Đơn hàng không hợp lệ để từ chối.');
            }

            $this->bookingRepository->update($id, [
                'trang_thai_don' => 'da_huy',
                'huy_boi' => 'nha_cung_cap',
                'ly_do_huy' => $reason,
            ]);
            $this->broadcastBooking($booking->fresh());

            $this->notifyCustomer(
                $booking->khach_hang_id,
                'Booking đã bị từ chối',
                "Đơn {$booking->ma_don} đã bị nhà cung cấp từ chối. Lý do: {$reason}",
                'booking_rejected',
            );

            return true;
        });
    }

    public function providerCompleteBooking(int $id, int $providerId): bool
    {
        return DB::transaction(function () use ($id, $providerId): bool {
            $booking = DonDatLich::query()
                ->where('nha_cung_cap_id', $providerId)
                ->lockForUpdate()
                ->find($id);

            if (!$booking || $booking->trang_thai_don !== 'da_xac_nhan') {
                throw new Exception(
                    'Đơn hàng chưa được xác nhận, không thể hoàn thành.',
                );
            }

            // --- Bắt đầu tính toán phân chia doanh thu ---
            // 1. Phí nền tảng (nếu chưa có thì lấy theo cấu hình)
            $phiNenTang = (float) $booking->phi_nen_tang;
            if ($phiNenTang <= 0) {
                $feePercent =
                    \App\Models\CauHinh::where(
                        'key',
                        'platform_fee_percent',
                    )->value('value') ?? 10;
                $phiNenTang =
                    (float) $booking->tong_tien * ((float) $feePercent / 100);
                $booking->phi_nen_tang = $phiNenTang;
            }

            $tienCoc = (float) $booking->tien_coc;

            // 2. Tiền nhà cung cấp nhận được từ ví hệ thống = Tiền cọc hệ thống đã thu - Phí nền tảng
            // Nếu số này > 0: Hệ thống trả thêm tiền cho thợ (vì khách cọc nhiều hơn phí)
            // Nếu số này < 0: Thợ bị trừ tiền trong ví (vì khách cọc ít hơn phí hoặc không cọc, thợ đã cầm tiền mặt toàn bộ)
            $tienChuyenVaoVi = $tienCoc - $phiNenTang;

            // Cập nhật số dư trong ví nhà cung cấp
            $hoSo = \App\Models\HoSoNhaCungCap::query()
                ->lockForUpdate()
                ->find($providerId);
            if ($hoSo) {
                $hoSo->so_du += $tienChuyenVaoVi;
                $hoSo->save();

                // Ghi nhận giao dịch
                if ($tienChuyenVaoVi != 0) {
                    \App\Models\GiaoDich::create([
                        'nguoi_dung_id' => $providerId,
                        'don_dat_lich_id' => $booking->id,
                        'loai_giao_dich' =>
                            $tienChuyenVaoVi > 0
                                ? 'thu_nhap'
                                : 'thu_phi_nen_tang',
                        'so_tien' => abs($tienChuyenVaoVi),
                        'phuong_thuc' => 'vi_noi_bo',
                        'trang_thai' => 'thanh_cong',
                        'ghi_chu' =>
                            $tienChuyenVaoVi > 0
                                ? "Nhận tiền thanh toán đơn {$booking->ma_don} (Cọc: {$tienCoc} - Phí: {$phiNenTang})"
                                : "Trừ phí nền tảng đơn {$booking->ma_don} (Phí: {$phiNenTang} - Cọc: {$tienCoc})",
                    ]);
                }
            }

            $this->bookingRepository->update($id, [
                'trang_thai_don' => 'hoan_thanh',
                'trang_thai_thanh_toan' => 'da_thanh_toan',
                'phi_nen_tang' => $phiNenTang,
            ]);
            $this->broadcastBooking($booking->fresh());

            $this->notifyCustomer(
                $booking->khach_hang_id,
                'Dịch vụ đã hoàn thành',
                "Đơn {$booking->ma_don} đã hoàn thành. Hãy đánh giá dịch vụ!",
                'booking_completed',
            );

            return true;
        });
    }

    public function adminForceConfirm(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $booking = DonDatLich::query()->lockForUpdate()->find($id);
            if (!$booking) {
                throw new Exception('Không tìm thấy đơn hàng.');
            }

            if ($booking->trang_thai_don !== 'cho_xac_nhan') {
                throw new Exception(
                    'Đơn hàng không ở trạng thái chờ xác nhận.',
                );
            }

            $this->bookingRepository->update($id, [
                'trang_thai_don' => 'da_xac_nhan',
            ]);
            $this->broadcastBooking($booking->fresh());

            $this->notifyCustomer(
                $booking->khach_hang_id,
                'Booking đã được xác nhận',
                "Đơn {$booking->ma_don} đã được Admin xác nhận.",
                'booking_confirmed',
            );
            $this->notifyProvider(
                $booking->nha_cung_cap_id,
                'Admin đã xác nhận booking',
                "Đơn {$booking->ma_don} đã được Admin xác nhận thay bạn.",
                'booking_confirmed',
            );

            return true;
        });
    }

    public function adminForceComplete(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $booking = DonDatLich::query()->lockForUpdate()->find($id);
            if (!$booking) {
                throw new Exception('Không tìm thấy đơn hàng.');
            }

            if (
                !in_array(
                    $booking->trang_thai_don,
                    ['da_xac_nhan', 'dang_thuc_hien'],
                    true,
                )
            ) {
                throw new Exception(
                    'Đơn hàng không ở trạng thái có thể hoàn thành.',
                );
            }

            // --- Bắt đầu tính toán phân chia doanh thu ---
            $phiNenTang = (float) $booking->phi_nen_tang;
            if ($phiNenTang <= 0) {
                $feePercent =
                    \App\Models\CauHinh::where(
                        'key',
                        'platform_fee_percent',
                    )->value('value') ?? 10;
                $phiNenTang =
                    (float) $booking->tong_tien * ((float) $feePercent / 100);
                $booking->phi_nen_tang = $phiNenTang;
            }

            $tienCoc = (float) $booking->tien_coc;
            $tienChuyenVaoVi = $tienCoc - $phiNenTang;

            // Cập nhật số dư trong ví nhà cung cấp
            $hoSo = \App\Models\HoSoNhaCungCap::query()
                ->lockForUpdate()
                ->find($booking->nha_cung_cap_id);
            if ($hoSo) {
                $hoSo->so_du += $tienChuyenVaoVi;
                $hoSo->save();

                // Ghi nhận giao dịch
                if ($tienChuyenVaoVi != 0) {
                    \App\Models\GiaoDich::create([
                        'nguoi_dung_id' => $booking->nha_cung_cap_id,
                        'don_dat_lich_id' => $booking->id,
                        'loai_giao_dich' =>
                            $tienChuyenVaoVi > 0
                                ? 'thu_nhap'
                                : 'thu_phi_nen_tang',
                        'so_tien' => abs($tienChuyenVaoVi),
                        'phuong_thuc' => 'vi_noi_bo',
                        'trang_thai' => 'thanh_cong',
                        'ghi_chu' =>
                            $tienChuyenVaoVi > 0
                                ? "Nhận tiền thanh toán đơn {$booking->ma_don} (Cọc: {$tienCoc} - Phí: {$phiNenTang}) (Admin xác nhận)"
                                : "Trừ phí nền tảng đơn {$booking->ma_don} (Phí: {$phiNenTang} - Cọc: {$tienCoc}) (Admin xác nhận)",
                    ]);
                }
            }

            $this->bookingRepository->update($id, [
                'trang_thai_don' => 'hoan_thanh',
                'trang_thai_thanh_toan' => 'da_thanh_toan',
                'phi_nen_tang' => $phiNenTang,
            ]);
            $this->broadcastBooking($booking->fresh());

            $this->notifyCustomer(
                $booking->khach_hang_id,
                'Dịch vụ đã hoàn thành',
                "Đơn {$booking->ma_don} đã được Admin đánh dấu hoàn thành.",
                'booking_completed',
            );
            $this->notifyProvider(
                $booking->nha_cung_cap_id,
                'Dịch vụ đã hoàn thành',
                "Đơn {$booking->ma_don} đã được Admin đánh dấu hoàn thành.",
                'booking_completed',
            );

            return true;
        });
    }

    public function adminForceReject(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $booking = DonDatLich::query()->lockForUpdate()->find($id);
            if (!$booking) {
                throw new Exception('Không tìm thấy đơn hàng.');
            }

            if (
                in_array(
                    $booking->trang_thai_don,
                    ['hoan_thanh', 'da_huy'],
                    true,
                )
            ) {
                throw new Exception(
                    'Không thể hủy đơn hàng đã ở trạng thái kết thúc.',
                );
            }

            $this->bookingRepository->update($id, [
                'trang_thai_don' => 'da_huy',
                'huy_boi' => 'admin',
                'ly_do_huy' => 'Bị hủy bởi Quản trị viên (Admin)',
            ]);
            $this->broadcastBooking($booking->fresh());

            $this->notifyCustomer(
                $booking->khach_hang_id,
                'Booking đã bị hủy',
                "Đơn {$booking->ma_don} đã bị Admin hệ thống hủy bỏ.",
                'booking_cancelled',
            );
            $this->notifyProvider(
                $booking->nha_cung_cap_id,
                'Booking đã bị hủy',
                "Đơn {$booking->ma_don} đã bị Admin hệ thống hủy bỏ.",
                'booking_cancelled',
            );

            return true;
        });
    }

    private function notifyCustomer(
        int $userId,
        string $title,
        string $body,
        string $type,
    ): void {
        $this->notificationService->createNotification([
            'nguoi_dung_id' => $userId,
            'tieu_de' => $title,
            'noi_dung' => $body,
            'loai_thong_bao' => $type,
            'da_doc' => false,
        ]);
    }

    private function notifyProvider(
        int $userId,
        string $title,
        string $body,
        string $type,
    ): void {
        $this->notificationService->createNotification([
            'nguoi_dung_id' => $userId,
            'tieu_de' => $title,
            'noi_dung' => $body,
            'loai_thong_bao' => $type,
            'da_doc' => false,
        ]);
    }

    private function broadcastBooking(DonDatLich $booking): void
    {
        $pendingCount = DonDatLich::where(
            'nha_cung_cap_id',
            $booking->nha_cung_cap_id,
        )
            ->where('trang_thai_don', 'cho_xac_nhan')
            ->count();

        broadcast(
            new BookingUpdated(
                $booking,
                $booking->khach_hang_id,
                $pendingCount,
            ),
        )->toOthers();
        broadcast(
            new BookingUpdated(
                $booking,
                $booking->nha_cung_cap_id,
                $pendingCount,
            ),
        )->toOthers();
    }
}
