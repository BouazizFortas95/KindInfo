<?php

use App\Models\Badge;
use App\Models\User;
use App\Models\Course;
use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Certificate download route
Route::get('/certificates/{uuid}/download', [CertificateController::class, 'download'])->name('certificates.download');

// Temporary route to check database content
Route::get('/check-db', function () {
    $badges = Badge::with('translations')->get();
    $badgeTranslationCount = \DB::table('badge_translations')->count();
    
    $data = [
        'badges_count' => Badge::count(),
        'users_count' => User::count(),
        'courses_count' => Course::count(),
        'badge_translations_count' => $badgeTranslationCount,
        'badges' => $badges->map(function ($badge) {
            return [
                'id' => $badge->id,
                'type' => $badge->type,
                'points_required' => $badge->points_required,
                'translations_count' => $badge->translations->count(),
                'translations' => $badge->translations->map(fn($t) => [
                    'locale' => $t->locale,
                    'name' => $t->name,
                    'description' => $t->description
                ]),
                'current_name' => $badge->name ?? 'NO NAME',
                'current_description' => $badge->description ?? 'NO DESC'
            ];
        }),
        'users' => User::latest()->take(3)->get(['id', 'name', 'email', 'created_at']),
        'courses' => Course::latest()->take(3)->get(['id', 'created_at']),
    ];
    
    return response()->json($data, 200, [], JSON_PRETTY_PRINT);
});
