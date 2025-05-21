<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    protected $fillable = [
        'course',
        'year',
        'semester',
        'abbreviation',
        'name',
        'name_pt',
        'ECTS',
        'hours',
        'optional',
    ];

    public $timestamps = false;

    public function getSemesterDescriptionAttribute()
    {
        return match ($this->semester) {
            0       => "Anual",
            1       => "1st",
            2       => "2nd",
            default => '?'
        };
    }    
}
