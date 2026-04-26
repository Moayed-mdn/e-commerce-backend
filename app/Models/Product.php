<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // 'slug' ← REMOVED
        'category_id',
        'brand_id',
        'product_variant_id',
        'is_active',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function activeVariants()
    {
        return $this->variants()->where('is_active', true);
    }

    public function defaultVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');

    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true);
    }

    // ── Translation Helpers ────────────────────────────────────

    /**
     * Get the translation for a given locale, with fallback.
     */
    public function translation(?string $locale = null): ?ProductTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->where('locale', $locale)->first()
            ?? $this->translations->first();
    }

    /**
     * Get a translated attribute value.
     */
    public function translated(string $key, ?string $locale = null): ?string
    {
        return $this->translation($locale)?->{$key};
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeActive($q)
    {
        return $q->where('products.is_active', 1);
    }

    // ── Static Finders ─────────────────────────────────────────

    /**
     * Find a product by its localized slug.
     * Searches product_translations for the current locale first,
     * then falls back to any locale.
     */
    public static function findBySlug(string $slug, ?string $locale = null): ?self
    {
        $locale = $locale ?? app()->getLocale();

        // Try exact locale match first
        $translation = ProductTranslation::where('slug', $slug)
            ->where('locale', $locale)
            ->first();

        // Fallback: any locale
        if (!$translation) {
            $translation = ProductTranslation::where('slug', $slug)->first();
        }

        if (!$translation) {
            return null;
        }

        return static::find($translation->product_id);
    }

    /**
     * Find by slug or fail with 404.
     */
    public static function findBySlugOrFail(string $slug, ?string $locale = null): self
    {
        $product = static::findBySlug($slug, $locale);

        if (!$product) {
            abort(404, 'Product not found.');
        }

        return $product;
    }

    // ── Display Helpers ────────────────────────────────────────

    public function getDisplayVariantAttribute()
    {
        return $this->defaultVariant ?? $this->activeVariants->first();
    }

      // ── Search Helpers ─────────────────────────────────────────

    /**
     * Get the primary image URL from the display variant.
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        $variant = $this->display_variant;

        if (!$variant) {
            return null;
        }

        // Use eager-loaded images if available
        $primaryImage = $variant->relationLoaded('images')
            ? $variant->images->where('is_primary', true)->first()
            : $variant->primary_image;

        return $primaryImage?->image_url;
    }

    /**
     * Computed average rating (from eager-loaded reviews).
     */
    public function getAvgRatingAttribute(): ?float
    {
        if ($this->relationLoaded('approvedReviews')) {
            $avg = $this->approvedReviews->avg('rating');
            return $avg ? round($avg, 1) : null;
        }

        return round($this->approvedReviews()->avg('rating'), 1) ?: null;
    }

    /**
     * Computed review count.
     */
    public function getReviewsCountAttribute(): int
    {
        if ($this->relationLoaded('approvedReviews')) {
            return $this->approvedReviews->count();
        }

        return $this->approvedReviews()->count();
    }

}