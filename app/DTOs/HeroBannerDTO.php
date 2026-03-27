<?php

namespace App\DTOs;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

#[TypeScript]
class HeroBannerDTO
{
    public function __construct(
        public int $id,
        public ?string $title,
        public ?string $subtitle,
        public ?string $cat_text,
        public string $cat_url,
        public int $position,
        
        #[LiteralTypeScriptType('{ type: "image"; img_url: string } | { type: "gradient"; gradient_from: string; gradient_to: string }')]
        public array $visual,
    ) {}
}
