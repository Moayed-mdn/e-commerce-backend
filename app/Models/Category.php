<?php
// app/Models/Category.php

namespace App\Models;

use App\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'parent_id',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parents()
    {
        return $this->parent()->with('parents');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // ── Translation Helpers ────────────────────────────────────

    /**
     * Get the translation for a given locale, with fallback.
     */
    public function translation(?string $locale = null): ?CategoryTranslation
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

    // ── Static Finders ─────────────────────────────────────────

    /**
     * Find a category by its localized slug.
     */
    public static function findByLocalizedSlug(string $slug, ?string $locale = null): ?self
    {
        $locale = $locale ?? app()->getLocale();

        // Try exact locale match first
        $translation = CategoryTranslation::where('slug', $slug)
            ->where('locale', $locale)
            ->first();

        // Fallback: any locale
        if (!$translation) {
            $translation = CategoryTranslation::where('slug', $slug)->first();
        }

        // Fallback: main table slug
        if (!$translation) {
            return static::where('slug', $slug)->first();
        }

        return static::find($translation->category_id);
    }

    /**
     * Find by localized slug or fail with 404.
     */
    public static function findByLocalizedSlugOrFail(string $slug, ?string $locale = null): self
    {
        $category = static::findByLocalizedSlug($slug, $locale);

        if (!$category) {
            throw new NotFoundException('Category not found.');
        }

        return $category;
    }

    /**
     * Collect all descendant IDs (flat array including self).
     */
    public function allDescendantIds(): array
    {
        $ids = [$this->id];
        $this->loadMissing('descendants');

        $this->collectDescendantIds($this, $ids);

        return $ids;
    }

    private function collectDescendantIds(Category $category, array &$ids): void
    {
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $this->collectDescendantIds($child, $ids);
        }
    }

    // ── Breadcrumb (fixed: uses translations) ──────────────────

    public function getBreadcrumbAttribute(): \Illuminate\Support\Collection
    {
        $locale = app()->getLocale();
        $breadcrumb = collect();
        $current = $this;
        $current->loadMissing('parents.translations');

        while ($current) {
            $translation = $current->translation($locale);

            $breadcrumb->prepend([
                'id'   => $current->id,
                'name' => $translation?->name ?? $current->slug,
                'slug' => $translation?->slug ?? $current->slug,
            ]);

            $current = $current->parent;
        }

        return $breadcrumb;
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    // ── Display Helper ─────────────────────────────────────────

    public function rootParent()
    {
        if ($this->parent) {
            return $this->parent->rootParent();
        }
        return $this;
    }
}