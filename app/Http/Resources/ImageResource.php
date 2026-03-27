<?php
// app/Http/Resources/ImageResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url' => $this->full_url,
            'alt_text' => $this->alt_text,
            'is_primary' => $this->is_primary,
        ];
    }
}