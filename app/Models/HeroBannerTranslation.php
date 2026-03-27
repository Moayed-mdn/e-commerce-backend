<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroBannerTranslation extends Model
{
    protected $fillable = [
        'hero_banner_id',
        'locale',
        'title',
        'subtitle',
        'cta_text',
    ];

    public function banner(){
        
        return $this->belongsTo(HeroBanner::class);
    }
}