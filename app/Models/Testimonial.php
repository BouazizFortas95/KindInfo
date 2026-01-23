<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['content', 'client_title'];

    protected $fillable = [
        'client_name',
        'client_company',
        'client_avatar',
        'rating',
        'is_visible',
        'order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'rating' => 'integer',
        'order' => 'integer',
    ];
}
