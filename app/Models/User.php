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

    use HasFactory;
    use Notifiable;
    use SoftDeletes;

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

  
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function firstLastName()
    {
        $names = explode(' ', $this->name);
        $first = $names[0] ?? '';
        $last = end($names);
        return trim("$first $last");
    }

    public function firstLastInitial()
    {
       
        $names = explode(' ', $this->name);
        $firstInitial = strtoupper(substr($names[0] ?? '', 0, 1));
        $lastInitial = strtoupper(substr(end($names), 0, 1));
        return $firstInitial . $lastInitial;
    }

     public function getPhotoFullUrlAttribute()
     {
        if ($this->photo) {
                return asset('storage/users/' . $this->photo);
            }
            return null;
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
