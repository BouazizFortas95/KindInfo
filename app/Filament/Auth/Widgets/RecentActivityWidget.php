<?php

namespace App\Filament\Auth\Widgets;

use App\Models\Lesson;
use App\Models\User;
use App\Models\Badge;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RecentActivityWidget extends Widget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    protected string $view = 'filament.widgets.recent-activity-widget';
    
    public function getViewData(): array
    {
        $locale = app()->getLocale();
        $user = Auth::user();
        $activities = [];
        
        // Get user's recent lesson completions
        $userRecentLessons = $user->lessons()
            ->where('lesson_user.progress', '>=', 100)
            ->withPivot('updated_at')
            ->orderBy('lesson_user.updated_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($userRecentLessons as $lesson) {
            $translation = $lesson->getTranslation($locale);
            $activities[] = [
                'type' => 'lesson',
                'title' => $translation->title ?? 'Untitled Lesson',
                'description' => __('general.you_completed'),
                'timestamp' => Carbon::parse($lesson->pivot->updated_at)->diffForHumans(),
                'date' => $lesson->pivot->updated_at,
            ];
        }
        
        // Get user's recent badge achievements
        $userRecentBadges = $user->badges()
            ->withPivot('earned_at')
            ->orderBy('user_badges.earned_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($userRecentBadges as $badge) {
            $translation = $badge->getTranslation($locale);
            $activities[] = [
                'type' => 'badge',
                'title' => $translation->name ?? 'Untitled Badge',
                'description' => __('general.you_earned'),
                'timestamp' => Carbon::parse($badge->pivot->earned_at)->diffForHumans(),
                'date' => $badge->pivot->earned_at,
            ];
        }
        
        // Sort activities by date
        usort($activities, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });
        
        // Limit to 10 most recent activities
        $activities = array_slice($activities, 0, 10);
        
        return [
            'activities' => $activities,
        ];
    }
}
