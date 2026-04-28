<?php

namespace App\Repositories\Contracts\Category;

use App\Data\DTOs\Category\CategoryIndexData;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Get marketplace categories for the public listing page.
     */
    public function getListing(CategoryIndexData $filters): Collection;
}