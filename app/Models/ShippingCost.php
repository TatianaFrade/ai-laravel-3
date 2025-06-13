<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;

class ShippingCost extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'settings_shipping_costs';

    
      protected $fillable = [
        'min_value_threshold',
        'max_value_threshold',
        'shipping_cost',
    ];

    /**
     * Find the appropriate shipping cost for a given order total
     *
     * @param float $orderTotal
     * @return float
     */
    public static function getShippingCostForTotal(float $orderTotal): float
    {
        $shippingCost = self::where('min_value_threshold', '<=', $orderTotal)
            ->where('max_value_threshold', '>=', $orderTotal)
            ->value('shipping_cost');
            
        return $shippingCost ?? 0.0;
    }
}
