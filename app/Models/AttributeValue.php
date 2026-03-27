<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
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
}