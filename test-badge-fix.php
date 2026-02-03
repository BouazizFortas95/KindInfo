<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Models\Badge;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel application
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== BADGE TRANSLATION FIX ===\n";

// Check current badge translations
$badgeTranslations = DB::table('badge_translations')->count();
echo "Current badge translations: {$badgeTranslations}\n";

$badges = Badge::all();
echo "Total badges: " . $badges->count() . "\n\n";

// Add missing translations if needed
$translationData = [
    'course_completion' => [
        'en' => ['name' => 'Course Completion', 'description' => 'Awarded for completing a course'],
        'ar' => ['name' => 'إتمام الدورة', 'description' => 'يُمنح عند إتمام دورة'],
    ],
    'points_achievement_100' => [
        'en' => ['name' => 'Bronze Badge', 'description' => 'Awarded for earning 100 points'],
        'ar' => ['name' => 'شارة البرونز', 'description' => 'يُمنح عند كسب 100 نقطة'],
    ],
    'points_achievement_500' => [
        'en' => ['name' => 'Silver Badge', 'description' => 'Awarded for earning 500 points'],
        'ar' => ['name' => 'شارة الفضة', 'description' => 'يُمنح عند كسب 500 نقطة'],
    ],
    'special_achievement' => [
        'en' => ['name' => 'Early Bird', 'description' => 'Awarded for joining in the first month'],
        'ar' => ['name' => 'العضو المبكر', 'description' => 'يُمنح للانضمام في الشهر الأول'],
    ],
];

foreach ($badges as $badge) {
    $key = $badge->type;
    if ($badge->type === 'points_achievement') {
        $key = $badge->type . '_' . $badge->points_required;
    }
    
    if (isset($translationData[$key])) {
        echo "Fixing badge {$badge->id} ({$badge->type}):\n";
        
        // Clear existing translations for this badge
        DB::table('badge_translations')->where('badge_id', $badge->id)->delete();
        
        foreach ($translationData[$key] as $locale => $data) {
            DB::table('badge_translations')->insert([
                'badge_id' => $badge->id,
                'locale' => $locale,
                'name' => $data['name'],
                'description' => $data['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "  Added {$locale}: {$data['name']}\n";
        }
    }
}

echo "\n=== VERIFICATION ===\n";
$badgeTranslations = DB::table('badge_translations')->count();
echo "Badge translations after fix: {$badgeTranslations}\n";

$badges = Badge::with('translations')->get();
foreach ($badges as $badge) {
    echo "Badge {$badge->id} ({$badge->type}): {$badge->translations->count()} translations\n";
    foreach ($badge->translations as $t) {
        echo "  - {$t->locale}: {$t->name}\n";
    }
}

echo "\nDone!\n";