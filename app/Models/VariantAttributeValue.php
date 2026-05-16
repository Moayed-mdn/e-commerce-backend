<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantAttributeValue extends Model
{
    
    public $timestamps = false;

    protected $fillable = [
        'variant_id',
        'attribute_id',
        'attribute_value_id',
    ];
    
    /**
     * Get the variant that owns this pivot record.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get the attribute that this pivot record references.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * Get the attribute value that this pivot record references.
     */
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }

}
