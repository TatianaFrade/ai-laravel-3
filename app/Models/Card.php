<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Card extends Model
{
    protected $fillable = ['id', 'card_number', 'balance', 'created_at', 'updated_at', 'deleted_at'];


    protected $dates = ['created_at','updated_at','expiration_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cria um cartão virtual para o usuário passado.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\Card
     */
    public static function createForUser($user)
    {
        return self::create([
            'id' => $user->id,
            'card_number' => $user->id,
            'balance' => 0.0,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
            
        ]);
    }
}
