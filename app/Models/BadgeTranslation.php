<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeTranslation extends Model
{
    protected $fillable = [
        'badge_id',
        'locale',
        'name',
        'description',
    ];

    public $timestamps = false;
}