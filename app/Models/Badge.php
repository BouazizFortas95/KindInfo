<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'icon_path',
        'type',
        'points_required',
        'is_active',
    ];

    protected $translatedAttributes = [
        'name',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_required' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }
}