<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lessons = \App\Models\Lesson::whereNotNull('attachments')->get();
foreach ($lessons as $lesson) {
    if (!empty($lesson->attachments)) {
        echo "Lesson ID: " . $lesson->id . "\n";
        var_dump($lesson->attachments);
    }
}