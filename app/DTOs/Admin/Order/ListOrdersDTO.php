<?php

namespace App\DTOs\Admin\Order;

use App\Http\Requests\Admin\Order\ListOrdersRequest;

class ListOrdersDTO
{
    public function __construct(
        public int $storeId,
        public ?string $status = null,
        public ?string $search = null,
        public int $perPage = 15,
        public int $page = 1,
    ) {}

    public static function fromRequest(ListOrdersRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            status: $request->string('status'),
            search: $request->string('search'),
            perPage: $request->integer('per_page', 15),
            page: $request->integer('page', 1),
        );
    }
}
