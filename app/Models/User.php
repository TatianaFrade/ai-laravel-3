<?php
 
namespace App\Models;
 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
 
 
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
        'default_delivery_address',
        'nif',
        'default_payment_type',
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
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
 
    public function firstLastName()
    {
        $names = explode(' ', $this->name);
        $first = $names[0] ?? '';
        $last = end($names);
        
        if (count($names) === 1 || $first === $last) {
            return $first;
        }
        
        return trim("$first $last");
    }
 
    public function firstLastInitial()
    {
        $fullName = $this->firstLastName();
        return Str::of($fullName)
            ->explode(' ')
            ->map(fn(string $name) => strtoupper(substr($name, 0, 1)))
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
    }
 
     public function getImageUrlAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists("users/{$this->photo}")) {
            return asset("storage/users/{$this->photo}");
 
        } else {
            return asset("storage/users/anonymous.png");
        }
    }


    public function operations()
    {
        return $this->hasMany(Operation::class, 'card_id', 'id'); // Ajusta conforme necessário
    }

    /**
     * Get the date of the last membership payment
     * @return \DateTime|null
     */
    public function lastMembershipPaymentDate(): ?\DateTime
    {
        $lastPayment = $this->operations()
            ->where('debit_type', 'membership_fee')
            ->orderBy('date', 'desc')
            ->first();
        
        if (!$lastPayment) {
            return null;
        }

        return new \DateTime($lastPayment->date);
    }

    public function hasPaidMembership(): bool
    {
        return $this->operations()
            ->where('debit_type', 'membership_fee')
            ->exists();
    }

    public function isMembershipExpired(): bool
    {
        // First check if membership was ever paid
        if (!$this->hasPaidMembership()) {
            // All users (including board and employee) need to pay the membership fee at least once
            return true;
        }

        // For board and employee users, they only need to pay once
        if (in_array($this->type, ['board', 'employee'])) {
            return false;
        }

        // For regular members, check if it's been more than a year since last payment
        $lastPayment = $this->lastMembershipPaymentDate();
        if (!$lastPayment) {
            return true;
        }

        $now = new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));
        $expiryDate = clone $lastPayment;
        $expiryDate->modify('+1 year');
        
        return $now > $expiryDate;
    }

    public function showMembershipPayButton(): bool
    {
        // Show for all users who haven't paid at all
        if (!$this->hasPaidMembership()) {
            return true;
        }
        
        // For regular members, also show if the membership is expired
        if ($this->type === 'member') {
            $lastPayment = $this->lastMembershipPaymentDate();
            if (!$lastPayment) {
                return true;
            }

            $now = new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));
            $expiryDate = clone $lastPayment;
            $expiryDate->modify('+1 year');
            
            return $now > $expiryDate;
        }
        
        // For board and employee users, don't show the button once they've paid
        return false;
    }

}