<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['name', 'slug'];
    protected $fillable = ['parent_id', 'form_locale'];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function en(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CategoryTranslation::class)->where('locale', 'en');
    }

    public function ar(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CategoryTranslation::class)->where('locale', 'ar');
    }

    public function fr(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CategoryTranslation::class)->where('locale', 'fr');
    }
}
