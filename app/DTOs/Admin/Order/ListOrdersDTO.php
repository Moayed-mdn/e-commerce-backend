<?php

namespace App\DTOs\Admin\Order;

use App\Http\Requests\Admin\Order\ListOrdersRequest;

class ListOrdersDTO
{
    public function __construct(
        public int $storeId,
        public ?string $search = null,
        public ?string $status = null,
        public int $perPage = 15,
        public int $page = 1,
    ) {}

    public static function fromRequest(ListOrdersRequest $request, int $storeId): self
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
