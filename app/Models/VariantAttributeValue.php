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
    
    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class,'variant_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class,'attribute_id');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class,'attribute_value_id');
    }

}
