<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

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
