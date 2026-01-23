<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Setting extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['site_name', 'meta_title', 'meta_description', 'address'];

    protected $fillable = [
        'logo',
        'favicon',
        'email',
        'phone',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'github_url',
    ];

    public static function getSingleton()
    {
        return static::firstOrCreate([]);
    }
}