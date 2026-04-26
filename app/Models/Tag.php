<?php
// app/Models/Tag.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'type'];

    public function products()
    {
        return $this->morphedByMany(Product::class, 'taggable');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}