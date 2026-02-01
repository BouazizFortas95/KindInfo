<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Astrotomic\Translatable\Contracts\Translatable;
use Astrotomic\Translatable\Translatable as TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model implements Translatable
{
    use TranslatableTrait;

    protected $guarded = [];

    public array $translatedAttributes = [
        'title',
        'description',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    // protected function attachments(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn($value) => json_decode($value, true) ?? [],
    //         set: fn($value) => json_encode($value),
    //     );
    // }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'lesson_user')
            ->withPivot('progress', 'last_watched_at')
            ->withTimestamps();
    }
}
