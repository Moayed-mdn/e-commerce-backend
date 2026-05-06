<?php

namespace App\Http\Resources;

use App\Enums\HeroBanner\HeroVisualTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;


class HeroBannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->getTranslation();

        return [
            'id' => $this->id,
            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'cat_text' => $translation?->cat_text,
            'cat_url' => $this->cat_url,
            'position' => $this->position,
            'visual' => $this->visual_type === HeroVisualTypeEnum::IMAGE
                    ?
                 [
                    'type' => HeroVisualTypeEnum::IMAGE->value,
                    'img_url' => $this->image_url
                 ]:
                 [
                    'type' => HeroVisualTypeEnum::GRADIENT->value,
                    'gradient_from' => $this->gradient_from,
                    'gradient_to' => $this->gradient_to
                 ]
            
        ];
    }

   
}
