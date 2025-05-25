<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Operation extends Model
{
    protected $fillable = [
        'card_id','type'
        ,'value','date'
        ,'debit_type' 
        ,'credit_type'
        ,'payment_type'
        ,'payment_reference',
        'order_id','created_at',
        'updated_at'];


    protected $dates = ['created_at','updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function create($card,$operationDate,$type,$creditType,$value,$payment_type,$payment_reference,$order)
    {
        return self::create([
            'card_id' => $card->id,
            'type' => $type,
            'value' => $value,
            'date' => $operationDate->toDateString(),
            'debit_type' => $type == 'debit' ? 'order' : null,
            'credit_type' => $creditType,
            'payment_type' => $payment_type,
            'payment_reference' => $payment_reference,
            'order_id' => $order->id,
            'created_at' => $operationDate,
            'updated_at' => $operationDate,
        ]);
    }
}
