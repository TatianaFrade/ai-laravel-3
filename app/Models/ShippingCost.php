<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Str;

class ShippingCost extends Model
{
    use HasFactory;

    protected $table = 'settings_shipping_costs';

    
      protected $fillable = [
        'min_value_threshold',
        'max_value_threshold',
        'shipping_cost',
    ];

}
