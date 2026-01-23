<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Service extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['title', 'slug', 'description'];

    protected $fillable = ['icon', 'is_active', 'order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getTranslatableAttributes(): array
    {
        return $this->translatedAttributes;
    }
}
