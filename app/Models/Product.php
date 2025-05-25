<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'stock',
        'description',
        'photo',
        'stock_lower_limit',
        'stock_upper_limit',
        'discount_min_qty', 
        'discount',


    ];

    /**
     * Obter as iniciais do produto (nome).
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1)->upper())
            ->implode('');
    }

    /**
     * Relação com a categoria.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
