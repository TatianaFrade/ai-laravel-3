<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    protected $fillable = [
        'card_id',
        'type',
        'value',
        'date',
        'debit_type',
        'credit_type',
        'payment_type',
        'payment_reference',
        'order_id'
    ];
    
    protected $casts = [
        'date' => 'date', // Garante que a data seja tratada corretamente
    ];

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Registra uma nova operação de crédito ou débito.
     *
     * @param array $data
     * @return self
     */
    public static function register(array $data)
    {
        return self::create($data);
    }
    public $timestamps = false; 
}
