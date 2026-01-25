<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable;
use Astrotomic\Translatable\Translatable as TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model implements Translatable
{
    use TranslatableTrait;

    protected $fillable = [
        'course_id',
        'video_url',
        'sort_order',
    ];

    public array $translatedAttributes = [
        'title',
        'description',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
