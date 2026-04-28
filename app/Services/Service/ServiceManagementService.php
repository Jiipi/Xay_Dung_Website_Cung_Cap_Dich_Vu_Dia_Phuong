<?php

namespace App\Services\Service;

use App\Repositories\Contracts\Service\ServiceRepositoryInterface;
use Illuminate\Support\Str;
use Exception;

class ServiceManagementService
{
    public function __construct(
        protected ServiceRepositoryInterface $serviceRepository
    ) {}

    public function createService(array $data, int $providerId): array
    {
        $baseSlug = Str::slug($data['ten_dich_vu']);
        $slug = $baseSlug;
        $counter = 1;
        
        while (!$this->serviceRepository->isSlugUniqueForProvider($slug, $providerId)) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $serviceData = [
            'nha_cung_cap_id' => $providerId,
            'danh_muc_id' => $data['danh_muc_id'],
            'ten_dich_vu' => $data['ten_dich_vu'],
            'slug' => $slug,
            'mo_ta_chi_tiet' => $data['mo_ta_chi_tiet'] ?? null,
            'gia_tu' => $data['gia_tu'] ?? null,
            'gia_den' => $data['gia_den'] ?? null,
            'don_vi_gia' => $data['don_vi_gia'] ?? null,
            'dia_chi_hien_thi' => $data['dia_chi_hien_thi'] ?? null,
            'danh_sach_anh' => $data['danh_sach_anh'] ?? null,
            'the_tu_khoa' => !empty($data['the_tu_khoa']) ? $data['the_tu_khoa'] : null,
            'khu_vuc_phuc_vu' => !empty($data['khu_vuc_phuc_vu']) ? $data['khu_vuc_phuc_vu'] : null,
            'trang_thai_duyet' => 'cho_duyet',
            'trang_thai_hoat_dong' => 'hoat_dong',
        ];

        $service = $this->serviceRepository->create($serviceData);

        return [
            'success' => true,
            'service_id' => $service->id,
        ];
    }

    public function updateService(int $serviceId, int $providerId, array $data, array $newImages = [], array $imagesToRemove = []): bool
    {
        $service = $this->serviceRepository->findByIdAndProvider($serviceId, $providerId);
        
        if (!$service) {
            throw new Exception("Không tìm thấy dịch vụ hoặc bạn không có quyền sửa.");
        }

        $updateData = [
            'ten_dich_vu' => $data['ten_dich_vu'],
            'danh_muc_id' => $data['danh_muc_id'],
            'mo_ta_chi_tiet' => $data['mo_ta_chi_tiet'] ?? null,
            'gia_tu' => $data['gia_tu'] ?? null,
            'gia_den' => $data['gia_den'] ?? null,
            'don_vi_gia' => $data['don_vi_gia'] ?? null,
            'dia_chi_hien_thi' => $data['dia_chi_hien_thi'] ?? null,
            'trang_thai_hoat_dong' => $data['trang_thai_hoat_dong'] ?? $service->trang_thai_hoat_dong,
        ];

        if (!empty($data['the_tu_khoa'])) {
            $updateData['the_tu_khoa'] = $data['the_tu_khoa'];
        }
        if (!empty($data['khu_vuc_phuc_vu'])) {
            $updateData['khu_vuc_phuc_vu'] = $data['khu_vuc_phuc_vu'];
        }

        $currentImages = $service->danh_sach_anh ?? [];
        
        if (!empty($imagesToRemove)) {
            $currentImages = array_values(array_diff($currentImages, $imagesToRemove));
        }
        
        if (!empty($newImages)) {
            $currentImages = array_merge($currentImages, $newImages);
        }
        
        $updateData['danh_sach_anh'] = $currentImages;

        if ($data['ten_dich_vu'] !== $service->ten_dich_vu) {
            $baseSlug = Str::slug($data['ten_dich_vu']);
            $slug = $baseSlug;
            $counter = 1;
            while (!$this->serviceRepository->isSlugUniqueForProvider($slug, $providerId, $serviceId)) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $updateData['slug'] = $slug;
        }

        return $this->serviceRepository->update($serviceId, $updateData);
    }

    public function deleteService(int $serviceId, int $providerId): bool
    {
        $service = $this->serviceRepository->findByIdAndProvider($serviceId, $providerId);
        
        if (!$service) {
            throw new Exception("Không tìm thấy dịch vụ hoặc bạn không có quyền xóa.");
        }

        $activeBookings = $service->donDatLich()
            ->whereIn('trang_thai_don', ['cho_xac_nhan', 'da_xac_nhan', 'dang_thuc_hien'])
            ->count();

        if ($activeBookings > 0) {
            throw new Exception("Không thể xóa dịch vụ đang có booking chưa hoàn thành.");
        }

        return $this->serviceRepository->update($serviceId, [
            'trang_thai_hoat_dong' => 'da_xoa',
        ]);
    }

    public function toggleServiceStatus(int $serviceId, int $providerId): string
    {
        $service = $this->serviceRepository->findByIdAndProvider($serviceId, $providerId);
        
        if (!$service) {
            throw new Exception("Không tìm thấy dịch vụ hoặc bạn không có quyền thay đổi.");
        }

        $newStatus = $service->trang_thai_hoat_dong === 'hoat_dong' ? 'tam_ngung' : 'hoat_dong';
        $this->serviceRepository->update($serviceId, ['trang_thai_hoat_dong' => $newStatus]);

        return $newStatus;
    }

    public function approveService(int $serviceId): bool
    {
        return $this->serviceRepository->update($serviceId, ['trang_thai_duyet' => 'da_duyet']);
    }

    public function rejectService(int $serviceId): bool
    {
        return $this->serviceRepository->update($serviceId, ['trang_thai_duyet' => 'tu_choi']);
    }
}
