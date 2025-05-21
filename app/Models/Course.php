<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    protected $fillable = [
        'abbreviation',
        'name',
        'name_pt',
        'type',
        'semesters',
        'ECTS',
        'places',
        'contact',
        'objectives',
        'objectives_pt',
    ];

    public $timestamps = false;
    protected $primaryKey = 'abbreviation';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getFullNameAttribute()
    {
        return match ($this->type) {
            'Master'    => "Master's in ",
            'TESP'      => 'TeSP - ',
            default     => ''
        } . $this->name;
    }

    public function getImageUrlAttribute()
    {
        $abrUpper = strtoupper(trim($this->abbreviation));
        if (Storage::disk('public')->exists("courses/$abrUpper.png")) {
            return asset("storage/courses/$abrUpper.png");
        } else {
            return asset("storage/courses/no_course.png");
        }
    }    
}
