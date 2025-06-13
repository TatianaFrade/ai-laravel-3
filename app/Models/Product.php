<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;


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


    
    public function getImageUrlAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists("products/{$this->photo}")) {
            return asset("storage/products/{$this->photo}");
        } else {
            return asset("storage/products/no_product.png");
        }
    }

    /**
     * Check if the product has an active discount
     *
     * @return bool
     */
    public function getHasActiveDiscountAttribute()
    {
        return $this->discount && $this->discount > 0 && (
            $this->discount_min_qty < $this->stock || 
            $this->stock <= $this->stock_lower_limit
        );
    }

    /**
     * Get the price after applying any active discount
     *
     * @return float
     */
    public function getPriceAfterDiscountAttribute()
    {
        return $this->has_active_discount ? $this->price - $this->discount : $this->price;
    }

    /**
     * Get the discount percentage if there is an active discount
     *
     * @return float
     */
    public function getDiscountPercentageAttribute()
    {
        return $this->has_active_discount ? ($this->discount / $this->price) * 100 : 0;
    }

    /**
     * Get total price for cart items
     *
     * @return float
     */
    public function getTotalPriceAttribute()
    {
        $quantity = $this->quantity ?? 1;
        return $this->price_after_discount * $quantity;
    }

    /**
     * Get the effective price after applying applicable discounts
     * 
     * @return float
     */
    public function getDiscountedPriceAttribute(): float
    {
        if ($this->discount_min_qty < $this->stock && $this->discount && $this->discount > 0) {
            return $this->price - $this->discount;
        }
        
        return $this->price;
    }

    /**
     * Calculate the total price for this product in the cart
     * 
     * @return float
     */
    public function getCartTotalAttribute(): float
    {
        return $this->discounted_price * ($this->quantity ?? 1);
    }
}
