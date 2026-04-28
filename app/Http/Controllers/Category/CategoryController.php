<?php

namespace App\Http\Controllers\Category;

use App\Data\DTOs\Category\CategoryIndexData;
use App\Http\Controllers\Controller;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
    }

    /**
     * Display the category catalog page.
     */
    public function index(Request $request): Response
    {
        $filters = CategoryIndexData::fromRequest($request);

        return Inertia::render('categories/Index', [
            'filters' => $filters->toArray(),
            'categories' => $this->categoryService->getCatalog($filters),
        ]);
    }
}