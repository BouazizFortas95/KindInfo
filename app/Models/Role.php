<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laratrust\Models\Role as LaratrustRole;

class Role extends LaratrustRole implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['display_name', 'description'];

    protected $fillable = ['name'];

    protected $translationModel = RoleTranslation::class;

    protected $with = ['translations'];

    public function getAttribute($key)
    {
        if ($key === null) {
            return null;
        }
        return parent::getAttribute($key);
    }

    public function getTranslatableAttributes(): array
    {
        return $this->translatedAttributes;
    }

    public function permissions(): BelongsToMany
{
    return $this->belongsToMany(
        config('laratrust.models.permission'),
        config('laratrust.tables.permission_role'),
        config('laratrust.foreign_keys.role'),
        config('laratrust.foreign_keys.permission')
    )
    ->join('permission_translations', function ($join) {
        $join->on('permissions.id', '=', 'permission_translations.permission_id')
             ->where('permission_translations.locale', '=', app()->getLocale());
    })
    ->select('permissions.*', 'permission_translations.display_name as translated_display_name')
    ->orderBy('permission_translations.display_name');
}
}

