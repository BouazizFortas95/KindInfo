<?php

namespace App\Filament\Widgets;

use App\Models\Badge;
use App\Models\Certificate;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AchievementStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(__('general.course_management'), \App\Models\Course::count())
                ->description(__('general.active_courses'))
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success')
                ->url(\App\Filament\Resources\Courses\CourseResource::getUrl()),

            Stat::make(__('courses.lessons'), \App\Models\Lesson::count())
                ->description(__('general.total_lessons'))
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('primary'),

            Stat::make(__('general.manage_badges'), Badge::count())
                ->description(__('general.recognition_badges'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning')
                ->url(\App\Filament\Resources\BadgeResource::getUrl()),

            Stat::make(__('general.manage_certificates'), Certificate::count())
                ->description(__('general.professional_certifications'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info')
                ->url(\App\Filament\Resources\CertificateResource::getUrl()),
        ];
    }
}
