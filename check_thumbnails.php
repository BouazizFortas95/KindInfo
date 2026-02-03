<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$courses = \App\Models\Course::whereNotNull('thumbnail')->get();
foreach ($courses as $course) {
    echo "Course ID: " . $course->id . "\n";
    var_dump($course->thumbnail);
}
