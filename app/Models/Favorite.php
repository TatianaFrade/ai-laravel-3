<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'product_id'];

    public $incrementing = false;
    protected $primaryKey = null;
    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function isFavorite($productId, $userId): bool
    {
        return self::where('user_id', $userId)
               ->where('product_id', $productId)
               ->exists();
    }

    public static function countFavorites($productId): bool
    {
        return self::where('product_id', $productId)->count();
    }
}
