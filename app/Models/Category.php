<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;


class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'image',
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists("categories/{$this->image}")) {
            return asset("storage/categories/{$this->image}");
        } else {
            return null;
        }
    }
}
