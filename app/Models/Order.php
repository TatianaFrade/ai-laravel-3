<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItemOrder;
use App\Models\User;

class Order extends Model
{

    protected $fillable = [
        'member_id',
        'status',
        'date',
        'total_items',       
        'shipping_cost',    
        'total',             
        'nif',        
        'delivery_address',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'member_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(ItemOrder::class);
    }

    public function canBeCompleted(): bool
    {
        foreach ($this->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return false;
            }
        }
        return true;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            $order->calculateTotal();
        });
        
        static::updating(function ($order) {
            $order->calculateTotal();
        });
    }

    public function calculateTotal()
    {
        // Calculate total products with discount applied
        $totalProducts = $this->items->sum(function ($item) {
            $price = $item->product->price * $item->quantity;
            $discount = $item->product->discount ?? 0;
            return $price * (1 - ($discount / 100));
        });

        // Add shipping cost
        $this->total = $totalProducts + ($this->shipping_cost ?? 0);
        
        return $this->total;
    }

    public function getTotalProductsAttribute()
    {
        return $this->items->sum(function ($item) {
            $price = $item->product->price * $item->quantity;
            $discount = $item->product->discount ?? 0;
            return $price * (1 - ($discount / 100));
        });
    }
}
