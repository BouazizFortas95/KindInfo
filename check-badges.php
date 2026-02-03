<?php

require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Badge;

echo "=== BADGE VERIFICATION ===\n";

$badges = Badge::with('translations')->get();

echo "Total badges: " . $badges->count() . "\n\n";

foreach ($badges as $badge) {
    echo "Badge #{$badge->id}: {$badge->type}\n";
    echo "  Translations: " . $badge->translations->count() . "\n";
    
    foreach ($badge->translations as $translation) {
        echo "  - {$translation->locale}: {$translation->name}\n";
    }
    
    echo "  Current name (app locale): " . ($badge->name ?? 'N/A') . "\n";
    echo "---\n";
}

echo "\n=== CHECKING TRANSLATION TABLES ===\n";

$badgeTranslations = \DB::table('badge_translations')->get();
echo "Badge translations count: " . $badgeTranslations->count() . "\n";

if ($badgeTranslations->count() > 0) {
    echo "Sample translations:\n";
    foreach ($badgeTranslations->take(3) as $trans) {
        echo "  Badge {$trans->badge_id} ({$trans->locale}): {$trans->name}\n";
    }
}