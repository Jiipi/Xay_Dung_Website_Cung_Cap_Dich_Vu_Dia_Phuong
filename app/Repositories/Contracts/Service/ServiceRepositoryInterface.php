<?php

namespace App\Repositories\Contracts\Service;

use App\Models\DichVu;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ServiceRepositoryInterface
{
    public function findById(int $id): ?DichVu;
    
    public function findBySlug(string $slug): ?DichVu;
    
    public function findByIdAndProvider(int $id, int $providerId): ?DichVu;
    
    public function getPaginatedForAdmin(?string $status, ?string $search, int $perPage = 15): LengthAwarePaginator;
    
    public function getStatusCountsForAdmin(): array;
    
    public function getPaginatedForProvider(int $providerId, ?string $status, ?string $search, int $perPage = 10): LengthAwarePaginator;
    
    public function getActivePublicServices(): Collection;
    
    public function create(array $data): DichVu;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function isSlugUniqueForProvider(string $slug, int $providerId, ?int $exceptId = null): bool;
}
