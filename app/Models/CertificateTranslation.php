<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTranslation extends Model
{
    protected $fillable = [
        'certificate_id',
        'locale',
        'title',
        'reason',
        'body',
    ];

    public $timestamps = false;
}