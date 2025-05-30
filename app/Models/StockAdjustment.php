<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class StockAdjustment extends Model
{
    use HasFactory;

    protected $table = 'stock_adjustments';

 
    protected $fillable = [
        'product_id',
        'registered_by_user_id',
        'quantity_changed',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }





}
