<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentActivity extends Model
{
    protected $table = 'recent_activities';

    // key is a string concatenation, so not auto-incrementing
    public $incrementing = false;
    protected $keyType = 'string';

    // View is read-only
    public $timestamps = false;

    protected $casts = [
        'activity_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
