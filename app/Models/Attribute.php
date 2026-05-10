<?php

namespace App\Models;

use App\Enums\Attribute\AttributeTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "attributes";

    protected $fillable = [
        'code',
        'type',
        'is_filterable',
        'is_visible_on_product',
        'sort_order',
    ];

    protected $casts = [
        'type'                 => AttributeTypeEnum::class,
        'is_filterable'        => 'boolean',
        'is_visible_on_product' => 'boolean',
        'sort_order'           => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(AttributeTranslation::class);
    }

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_attribute_values', 'attribute_id', 'variant_id');
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function translation(?string $locale = null): ?AttributeTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->where('locale', $locale)->first()
            ?? $this->translations->first();
    }
}
