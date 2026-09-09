<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class ExportProductsAction
{
    public function execute(int $storeId): string
    {
        $products = Product::query()
            ->where('store_id', $storeId)
            ->with(['translations', 'defaultVariant'])
            ->get();

        $csv = "SKU,Name,Price,Cost Price,Quantity,Active\n";

        foreach ($products as $product) {
            $name = optional($product->translations->first())->name ?? '';
            $variant = $product->defaultVariant;

            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $variant->sku ?? '',
                str_replace(',', ' ', $name),
                $variant->price ?? '',
                $variant->cost_price ?? '',
                $variant->quantity ?? '',
                $product->is_active ? 'yes' : 'no'
            );
        }

        $path = "stores/{$storeId}/exports/products-" . Carbon::now()->format('Y-m-d-His') . '.csv';

        Storage::disk('s3_private')->put($path, $csv);

        return $path;
    }
}
