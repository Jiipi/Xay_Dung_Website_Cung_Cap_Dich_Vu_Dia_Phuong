<?php

namespace App\Repositories\Contracts\Booking;

use App\Models\DonDatLich;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BookingRepositoryInterface
{
    public function findById(int $id): ?DonDatLich;
    
    public function findByIdAndCustomer(int $id, int $customerId): ?DonDatLich;
    
    public function findByIdAndProvider(int $id, int $providerId): ?DonDatLich;
    
    public function getPaginatedForProvider(int $providerId, ?string $status, ?string $search, int $perPage = 10): LengthAwarePaginator;
    
    public function getStatusCountsForProvider(int $providerId): array;
    
    public function getRecentForCustomer(int $customerId, int $limit = 5): Collection;
    
    public function getUpcomingForCustomer(int $customerId, int $limit = 3): Collection;
    
    public function getAllForCustomer(int $customerId): Collection;
    
    public function countForCustomer(int $customerId, ?string $status = null): int;
    
    public function countUpcomingForCustomer(int $customerId): int;
    
    public function countReviewPendingForCustomer(int $customerId): int;
    
    public function getPaginatedForAdmin(?string $status, ?string $search, int $perPage = 15): LengthAwarePaginator;
    
    public function getStatusCountsForAdmin(): array;
    
    public function create(array $data): DonDatLich;
    
    public function update(int $id, array $data): bool;
}
