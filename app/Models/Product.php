<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'stock',
        'description',
        'photo',
        'stock_lower_limit',
        'stock_upper_limit',
        'discount_min_qty', 
        'discount',
    ];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplyorders()
    {
        return $this->hasMany(SupplyOrder::class, 'product_id', 'id');
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }


}
