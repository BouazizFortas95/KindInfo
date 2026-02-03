<?php

namespace App\Filament\Auth\Widgets;

use App\Models\Badge;
use App\Models\Certificate;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AchievementStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        
        return [
            Stat::make(__('general.my_courses'), \App\Models\Course::whereHas('lessons.users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count())
                ->description(__('courses.enrolled_courses'))
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success')
                ->url(\App\Filament\Auth\Resources\Courses\CourseResource::getUrl()),

            Stat::make(__('courses.completed_lessons'), $user->lessons()->wherePivot('progress', '>=', 100)->count())
                ->description(__('courses.lessons_completed'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),

            Stat::make(__('general.my_badges'), $user->badges()->count())
                ->description(__('badges.earned_badges'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),

            Stat::make(__('general.my_certificates'), Certificate::where('user_id', $user->id)->count())
                ->description(__('certificates.earned_certificates'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
