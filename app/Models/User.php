<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class User extends Authenticatable implements MustVerifyEmail 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'blocked',
        'gender',
        'delivery_address',
        'nif',
        'payment_details',
        'photo',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

     public function isEmployee(): bool
    {
        return $this->type === 'employee';
    }

    public function isRegular(): bool
    {
        return $this->type === 'member';
    }

    public function isBoard(): bool
    {
        return $this->type === 'board';
    }

  

  
  public function card()
    {
        return $this->hasOne(Card::class, 'id', 'id');
        // card.id (chave primária do cartão) = user.id (chave primária do user)
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function supplyorders()
    {
        return $this->hasMany(SupplyOrder::class, 'registered_by_user_id', 'id');
    }


}
