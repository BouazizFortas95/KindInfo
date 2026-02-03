<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = [
        'course_id',
        'user_id',
        'certificate_uuid',
        'issued_at',
    ];

    protected $translatedAttributes = [
        'title',
        'reason',
        'body',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_uuid)) {
                $certificate->certificate_uuid = Str::uuid();
            }
            if (empty($certificate->issued_at)) {
                $certificate->issued_at = now();
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName()
    {
        return 'certificate_uuid';
    }
}