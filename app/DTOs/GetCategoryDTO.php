<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Category\GetCategoryRequest;

class GetCategoryDTO
{
    public function __construct(
        public int $id,
    ) {}

    public static function fromRequest(GetCategoryRequest $request): self
    {
        // Extract ID from the resolved category model or route parameter
        $category = $request->route('category');
        $id = $category instanceof \App\Models\Category ? $category->id : (int) $category;

        return new self($id);
    }
}
