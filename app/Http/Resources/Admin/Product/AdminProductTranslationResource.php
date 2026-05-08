<?php

namespace App\Http\Resources\Admin\Product;

use App\Support\ProductTranslationCompleteness;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductTranslationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'locale' => data_get($this->resource, 'locale'),
            'name' => data_get($this->resource, 'name'),
            'slug' => data_get($this->resource, 'slug'),
            'description' => data_get($this->resource, 'description'),
            'seo_title' => data_get($this->resource, 'seo_title'),
            'seo_description' => data_get($this->resource, 'seo_description'),
            'is_complete' => ProductTranslationCompleteness::isComplete($this->resource),
        ];
    }
}
