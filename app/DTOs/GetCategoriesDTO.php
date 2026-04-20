<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Category\GetCategoriesRequest;

class GetCategoriesDTO
{
    public function __construct(
        public ?string $parentId = null,
        public ?string $type = null,
    ) {}

    public static function fromRequest(GetCategoriesRequest $request): self
    {
        return new self(
            $request->input('parent_id'),
            $request->input('type'),
        );
    }
}
