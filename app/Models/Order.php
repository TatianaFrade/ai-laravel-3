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
            // Check if there's enough stock
            if ($item->product->stock < $item->quantity) {
                return false;
            }
            
            // Check if this will exceed the product's upper limit
            if ($item->product->stock_upper_limit && 
                ($item->product->stock > $item->product->stock_upper_limit)) {
                return false;
            }
        }
        return true;
    }
    
    public function getStockUpperLimitExceededProducts(): array
    {
        $exceededProducts = [];
        foreach ($this->items as $item) {
            if ($item->product->stock_upper_limit && 
                ($item->product->stock > $item->product->stock_upper_limit)) {
                $exceededProducts[] = [
                    'product' => $item->product,
                    'current_stock' => $item->product->stock,
                    'upper_limit' => $item->product->stock_upper_limit
                ];
            }
        }
        return $exceededProducts;
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
        if ($this->relationLoaded('items')) {
            $totalProducts = $this->items->sum(function ($item) {
                return $item->subtotal;
            });
        } else {
            return $this->total;
        }
 
        // Add shipping cost and round to 2 decimal places
        $this->total = round($totalProducts + ($this->shipping_cost ?? 0), 2);
        
        return $this->total;
    }
 
    public function getTotalProductsAttribute()
    {
        return $this->items->sum('subtotal');
    }
}