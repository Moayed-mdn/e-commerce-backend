<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'attribute_id',
        'code',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);    // ← ADDED
    }

    public function translations()
    {
        return $this->hasMany(AttributeValueTranslation::class);
    }

    public function translation(?string $locale = null): ?AttributeValueTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->where('locale', $locale)->first()
            ?? $this->translations->first();
    }
}