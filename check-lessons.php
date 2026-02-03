<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Models\Course;
use App\Models\Lesson;

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

echo "=== LESSON ANALYSIS ===\n";

$coursesCount = Course::count();
$lessonsCount = Lesson::count();

echo "Total Courses: {$coursesCount}\n";
echo "Total Lessons: {$lessonsCount}\n";

$coursesWithLessons = Course::has('lessons')->count();
$coursesWithoutLessons = Course::doesntHave('lessons')->count();

echo "Courses with lessons: {$coursesWithLessons}\n";
echo "Courses without lessons: {$coursesWithoutLessons}\n";

if ($coursesWithoutLessons > 0) {
    echo "\n=== COURSES WITHOUT LESSONS ===\n";
    $emptyCoures = Course::doesntHave('lessons')->with('translations')->take(5)->get();
    foreach ($emptyCoures as $course) {
        echo "- Course #{$course->id}: {$course->title}\n";
    }
    
    if ($coursesWithoutLessons > 5) {
        echo "... and " . ($coursesWithoutLessons - 5) . " more courses\n";
    }
}

echo "\n=== SAMPLE LESSONS ===\n";
$sampleLessons = Lesson::with('course', 'translations')->take(3)->get();
foreach ($sampleLessons as $lesson) {
    echo "- Lesson #{$lesson->id}: {$lesson->title} (Course: {$lesson->course->title})\n";
}

echo "\nDone!\n";