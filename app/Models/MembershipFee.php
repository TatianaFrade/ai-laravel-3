<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MembershipFee extends Model
{
    use HasFactory;

    protected $table = 'settings';


    protected $fillable = [
        'membership_fee',
    ];

}
