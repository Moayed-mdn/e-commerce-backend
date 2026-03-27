<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $table = "attributes";

    protected $fillable = [
        'code',
    ];

    public function translations()
    {
        return $this->hasMany(AttributeTranslation::class);
    }

    public function variatns()
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_id');
    }
}
