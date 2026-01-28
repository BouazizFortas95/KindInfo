<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Lesson extends Model
{
    use Translatable {
        fill as astrotomicFill;
    }

    protected $guarded = [];

    public array $translatedAttributes = [
        'title',
        'description',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

     public function fill(array $attributes)
    {
        return parent::fill($attributes);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
