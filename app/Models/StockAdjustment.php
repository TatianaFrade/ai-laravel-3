<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Str;

class StockAdjustment extends Model
{
    use HasFactory;

    // Indica explicitamente a tabela, já que o nome não segue a convenção
    protected $table = 'stock_adjustments';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
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

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

  


}
