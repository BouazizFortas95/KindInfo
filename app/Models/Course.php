<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable;
use Astrotomic\Translatable\Translatable as TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model implements Translatable
{
    use TranslatableTrait;

    protected $fillable = [
        'thumbnail',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public array $translatedAttributes = [
        'title',
        'description',
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }
}
