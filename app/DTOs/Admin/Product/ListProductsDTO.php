<?php

namespace App\DTOs\Admin\Product;

use App\Http\Requests\Admin\Product\ListProductsRequest;

class ListProductsDTO
{
    public function __construct(
        public int $storeId,
        public ?string $search = null,
        public ?string $status = null,
        public int $perPage = 15,
        public int $page = 1,
    ) {}

    public static function fromRequest(ListProductsRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            search: $request->string('search'),
            status: $request->string('status'),
            perPage: $request->integer('per_page', 15),
            page: $request->integer('page', 1),
        );
    }
}
