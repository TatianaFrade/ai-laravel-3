<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Str;

class ShippingCost extends Model
{
    use HasFactory;

    // Indica explicitamente a tabela, já que o nome não segue a convenção
    protected $table = 'settings_shipping_costs';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'min_value_threshold',
        'max_value_threshold',
        'shipping_cost',
        

    ];

}
