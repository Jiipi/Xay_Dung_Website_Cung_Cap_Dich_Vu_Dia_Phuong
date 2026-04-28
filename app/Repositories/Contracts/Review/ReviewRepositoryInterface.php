<?php

namespace App\Repositories\Contracts\Review;

use App\Models\DanhGia;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function findById(int $id): ?DanhGia;
    
    public function findByIdAndProvider(int $id, int $providerId): ?DanhGia;
    
    public function getPaginatedForProvider(int $providerId, ?int $stars, ?bool $unreplied, int $perPage = 10): LengthAwarePaginator;
    
    public function getStatsForProvider(int $providerId): array;
    
    public function getPaginatedForAdmin(?int $stars, ?string $search, int $perPage = 15): LengthAwarePaginator;
    
    public function getStatsForAdmin(): array;
    
    public function existsForBooking(int $bookingId): bool;
    
    public function create(array $data): DanhGia;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function calculateAverageRatingForProvider(int $providerId): float;
}
