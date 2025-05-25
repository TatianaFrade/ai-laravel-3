<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Str;

class MembershipFee extends Model
{
    use HasFactory;

    // Indica explicitamente a tabela, já que o nome não segue a convenção
    protected $table = 'settings';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'membership_fee',
    ];

    /**
     * Exemplo: obter iniciais do nome, se existir um campo "name".
     */
  
}
