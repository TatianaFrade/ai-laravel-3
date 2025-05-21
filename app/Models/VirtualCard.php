<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VirtualCard extends Model
{
    protected $fillable = ['user_id', 'card_number', 'expiration_date'];

    protected $dates = ['expiration_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cria um cartão virtual para o usuário passado.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\VirtualCard
     */
    public static function createForUser($user)
    {
        return self::create([
            'user_id' => $user->id,
            'card_number' => 'VIRT' . strtoupper(Str::random(12)),
            'expiration_date' => now()->addYear(),
        ]);
    }
}
