<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Str;

class SupplyOrder extends Model
{
    use HasFactory;


    protected $fillable = [
        'product_id',
        'registered_by_user_id',
        'status',
        'quantity',

    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'registered_by_user_id', 'id');
        
    }

}
