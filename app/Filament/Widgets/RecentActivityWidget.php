<?php

namespace App\Filament\Widgets;

use App\Models\Lesson;
use App\Models\User;
use App\Models\Badge;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class RecentActivityWidget extends Widget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    protected string $view = 'filament.widgets.recent-activity-widget';
    
    public function getViewData(): array
    {
        $locale = app()->getLocale();
        $activities = [];
        
        // Get recent lesson completions
        $recentLessons = Lesson::whereHas('users', function ($query) {
                $query->where('lesson_user.progress', '>=', 100)
                      ->orderBy('lesson_user.updated_at', 'desc');
            })
            ->with(['users' => function ($query) {
                $query->where('lesson_user.progress', '>=', 100)
                      ->orderBy('lesson_user.updated_at', 'desc')
                      ->limit(10);
            }])
            ->limit(5)
            ->get();
            
        foreach ($recentLessons as $lesson) {
            foreach ($lesson->users as $user) {
                if ($user->pivot->progress >= 100) {
                    $translation = $lesson->getTranslation($locale);
                    $activities[] = [
                        'type' => 'lesson',
                        'title' => $translation->title ?? 'Untitled Lesson',
                        'description' => __('general.completed_by', ['name' => $user->name]),
                        'timestamp' => Carbon::parse($user->pivot->updated_at)->diffForHumans(),
                        'date' => $user->pivot->updated_at,
                    ];
                }
            }
        }
        
        // Get recent badge achievements
        $recentBadges = Badge::whereHas('users', function ($query) {
                $query->orderBy('user_badges.earned_at', 'desc');
            })
            ->with(['users' => function ($query) {
                $query->orderBy('user_badges.earned_at', 'desc')
                      ->limit(10);
            }])
            ->limit(5)
            ->get();
            
        foreach ($recentBadges as $badge) {
            foreach ($badge->users as $user) {
                $translation = $badge->getTranslation($locale);
                $activities[] = [
                    'type' => 'badge',
                    'title' => $translation->name ?? 'Untitled Badge',
                    'description' => __('general.earned_by', ['name' => $user->name]),
                    'timestamp' => Carbon::parse($user->pivot->earned_at)->diffForHumans(),
                    'date' => $user->pivot->earned_at,
                ];
            }
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
