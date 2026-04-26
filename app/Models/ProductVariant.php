<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'quantity',
        'manufacture_date',
        'expiry_date',
        'batch_number',
        'is_active',
    ];

    public function casts(): array
    {
        return [
            'price' => 'float',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Existing relationship — now includes pivot data.
     * Used by: ProductController@show, CartController, etc.
     * Adding withPivot does NOT break existing code that ignores the pivot.
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'variant_attribute_values', 'variant_id')
            ->withPivot('attribute_value_id');   // ← ADDED
    }

    /**
     * NEW: Direct access to AttributeValue models through the pivot.
     * Used by: CartItemResource for translated attribute display.
     */
    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values',
            'variant_id',
            'attribute_value_id'
        )->withPivot('attribute_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first();
    }
}