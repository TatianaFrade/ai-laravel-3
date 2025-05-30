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




    
}
