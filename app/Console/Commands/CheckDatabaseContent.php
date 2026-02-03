<?php

namespace App\Console\Commands;

use App\Models\Badge;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabaseContent extends Command
{
    protected $signature = 'db:check-content';
    protected $description = 'Check database content for debugging';

    public function handle()
    {
        $this->info('=== DATABASE CONTENT CHECK ===');
        
        // Basic counts
        $badgesCount = Badge::count();
        $usersCount = User::count();
        $coursesCount = Course::count();
        $categoriesCount = Category::count();
        
        $this->table(['Table', 'Count'], [
            ['badges', $badgesCount],
            ['users', $usersCount], 
            ['courses', $coursesCount],
            ['categories', $categoriesCount],
        ]);

        // Badge details
        $this->info("\n=== BADGE DETAILS ===");
        if ($badgesCount > 0) {
            $badges = Badge::all();
            foreach ($badges as $badge) {
                $this->line("Badge ID: {$badge->id}");
                $this->line("  Type: {$badge->type}");
                $this->line("  Active: " . ($badge->is_active ? 'Yes' : 'No'));
                $this->line("  Points Required: {$badge->points_required}");
                $this->line("  Name: " . ($badge->name ?? 'No translation loaded'));
                $this->line("  Description: " . ($badge->description ?? 'No translation loaded'));
                $this->line("  Created: {$badge->created_at}");
                $this->line("---");
            }
        } else {
            $this->error("No badges found!");
        }

        // Badge translations
        $badgeTranslations = DB::table('badge_translations')->get();
        $this->info("\n=== BADGE TRANSLATIONS ===");
        $this->line("Badge translations count: " . $badgeTranslations->count());
        
        foreach ($badgeTranslations as $translation) {
            $this->line("Badge ID: {$translation->badge_id}, Locale: {$translation->locale}, Name: {$translation->name}");
        }

        // Recent data
        $this->info("\n=== RECENT RECORDS ===");
        $recentUsers = User::latest()->take(3)->get(['id', 'name', 'created_at']);
        foreach ($recentUsers as $user) {
            $this->line("User: {$user->id} - {$user->name} - {$user->created_at}");
        }

        return 0;
    }
}