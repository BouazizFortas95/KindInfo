<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
    ];
}
