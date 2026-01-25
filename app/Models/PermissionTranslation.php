<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['display_name', 'description'];
}
