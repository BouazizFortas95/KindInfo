<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Laratrust\Models\Permission as PermissionModel;

class Permission extends PermissionModel implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['display_name', 'description'];

    protected $fillable = ['name', 'display_name', 'description'];

    protected $translationModel = PermissionTranslation::class;

    public function getAttribute($key)
    {
        if ($key === null) {
            return null;
        }
        return parent::getAttribute($key);
    }
}
