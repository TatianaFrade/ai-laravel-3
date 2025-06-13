<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class StockAdjustment extends Model
{
    use HasFactory;

    protected $table = 'stock_adjustments';    protected $fillable = [
        'product_id',
        'registered_by_user_id',
        'quantity_changed'
    ];
    
    // Ensure no other attributes are filled
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    /**
     * Override the create method to ensure only valid attributes are used
     * 
     * @param array $attributes
     * @return \App\Models\StockAdjustment
     */
    public static function create(array $attributes = [])
    {
        // Only keep allowed attributes
        $validAttributes = array_intersect_key($attributes, array_flip([
            'product_id',
            'registered_by_user_id', 
            'quantity_changed'
        ]));
        
        return static::query()->create($validAttributes);
    }
}
