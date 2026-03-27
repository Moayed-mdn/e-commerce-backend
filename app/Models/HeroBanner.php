<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HeroBanner extends Model
{
    protected $fillable = [
        'cat_url',
        'position',
        'visual_type',
        'image_path',
        'gradient_from',
        'gradient_to',
        'is_active',
        'starts_at',
        'ends_at',
    ];
    public function translations(){

        return $this->hasMany(HeroBannerTranslation::class);
    }

    public function getTranslation($locale = null){
        
        $locale =  $locale ?? app()->getLocale();

        return 
            $this->translations()->firstWhere('locale',$locale) ??
            $this->translations()->firstWhere('locale',config('app.fallback_locale'));
    }

    public function scopeActive($q){

        return $q->where('is_active',true);
    }


    public function getImageUrlAttribute(){
        return $this->image_path? asset('storage/'.$this->image_path) : null;
    }

    // public function scopeDate($q){
    //     return $q->where('starts_at','<=',Carbon::now())
    //                 ->where('ends_at','>',Carbon::now());
    // }
}
