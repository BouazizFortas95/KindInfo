<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
    ];
}
