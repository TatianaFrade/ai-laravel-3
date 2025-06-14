<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
    protected $table = 'items_orders';
    
    
    protected $with = ['product'];
    
    protected $fillable = [
        'order_id', 
        'product_id', 
        'quantity', 
        'unit_price',
        'discount',
        'subtotal',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public $timestamps = false;
}