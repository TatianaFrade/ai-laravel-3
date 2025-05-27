<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['id', 'card_number', 'balance', 'created_at', 'updated_at', 'deleted_at'];

    protected $primaryKey = 'id';     
    public $incrementing = false;      

    protected $dates = ['created_at','updated_at','expiration_date'];

   public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
        // card.id (chave primária e estrangeira) = user.id
    }

    /**
     * Cria um cartão virtual para o usuário passado.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\Card
     */
    public static function createForUser($user)
    {
        $cardNumber = self::generateUniqueCardNumber();

        return self::create([
            'id' => $user->id,
            'card_number' => $cardNumber,
            'balance' => 0.0,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
        ]);
    }

    private static function generateUniqueCardNumber()
    {
        do {
            $number = random_int(100000, 999999);
        } while (self::where('card_number', $number)->exists());

        return $number;
    }
}
