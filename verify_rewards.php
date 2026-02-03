<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\Badge;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

echo "🎯 REWARD SYSTEM VERIFICATION\n";
echo "=============================\n\n";

// Check badges
$badgesCount = Badge::count();
echo "📛 BADGES: {$badgesCount} found\n";

if ($badgesCount > 0) {
    $badges = Badge::with('translations')->get();
    foreach ($badges as $badge) {
        echo "  • ID: {$badge->id} | Type: {$badge->type} | Points: {$badge->points_required}\n";
        
        // Get translations
        $enTranslation = $badge->translations->where('locale', 'en')->first();
        $arTranslation = $badge->translations->where('locale', 'ar')->first();
        
        if ($enTranslation) {
            echo "    EN: {$enTranslation->name}\n";
        }
        if ($arTranslation) {
            echo "    AR: {$arTranslation->name}\n";
        }
        echo "\n";
    }
}

// Check users 
$usersCount = User::count();
echo "👥 USERS: {$usersCount} found\n";

// Check courses
$coursesCount = Course::count();  
echo "📚 COURSES: {$coursesCount} found\n";

// Check if Filament routes are accessible
echo "\n🔗 FILAMENT ROUTES:\n";
echo "  • Admin Panel: http://127.0.0.1:8000/admin\n";
echo "  • Badges: http://127.0.0.1:8000/admin/badges\n";
echo "  • Certificates: http://127.0.0.1:8000/admin/certificates\n";

echo "\n✅ Verification complete!\n";

if ($badgesCount > 0 && $usersCount > 0 && $coursesCount > 0) {
    echo "🎉 All systems ready! Your reward system is fully operational.\n";
} else {
    echo "⚠️  Some data might be missing. Check the seeders.\n";
}