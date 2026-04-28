<?php

namespace App\Data\DTOs\Category;

use Illuminate\Http\Request;

readonly class CategoryIndexData
{
    public function __construct(
        public ?string $search = null,
        public string $status = 'hoat_dong',
        public bool $withSubcategories = true,
    ) {
    }

    /**
     * Create a DTO instance from the HTTP request.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->string('search')->trim()->value() ?: null,
            status: $request->string('status')->trim()->value() ?: 'hoat_dong',
            withSubcategories: $request->boolean('with_subcategories', true),
        );
    }

    /**
     * Convert the DTO into a simple array for Inertia props.
     *
     * @return array{search:?string,status:string,with_subcategories:bool}
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'status' => $this->status,
            'with_subcategories' => $this->withSubcategories,
        ];
    }
}