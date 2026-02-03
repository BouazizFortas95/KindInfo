<?php

namespace App\Filament\Actions;

use App\Models\Course;
use App\Models\Lesson;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class BulkGenerateLessonsAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('bulk_generate_lessons')
            ->label(__('courses.generate_lessons_bulk'))
            ->icon('heroicon-o-academic-cap')
            ->color('success')
            ->requiresConfirmation()
            ->form([
                Select::make('template')
                    ->label(__('courses.lesson_template'))
                    ->options([
                        'basic' => __('courses.basic_course'),
                        'intermediate' => __('courses.intermediate_course'),
                        'advanced' => __('courses.advanced_course'),
                        'custom' => __('courses.custom_number'),
                    ])
                    ->required()
                    ->reactive()
                    ->default('basic'),

                TextInput::make('custom_count')
                    ->label(__('courses.number_of_lessons'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(20)
                    ->default(5)
                    ->required()
                    ->visible(fn($get) => $get('template') === 'custom'),
            ])
            ->action(function (Collection $records, array $data): void {
                DB::transaction(function () use ($records, $data) {
                    $locale = App::getLocale();
                    $lessonCount = match ($data['template']) {
                        'basic' => 5,
                        'intermediate' => 8,
                        'advanced' => 12,
                        'custom' => (int) $data['custom_count'],
                        default => 5,
                    };

                    $totalCreated = 0;

                    foreach ($records as $course) {
                        $createdCount = self::createLessonsForCourse($course, $lessonCount, $locale);
                        $totalCreated += $createdCount;
                    }

                    Notification::make()
                        ->title(__('courses.generate_lessons'))
                        ->body(__('courses.success_created_bulk', [
                            'count' => $totalCreated,
                            'courses' => $records->count(),
                            'locale' => strtoupper($locale),
                        ]))
                        ->success()
                        ->send();
                });
            })
            ->deselectRecordsAfterCompletion()
            ->modalWidth('md')
            ->modalHeading(__('courses.generate_lessons_bulk'))
            ->modalSubmitActionLabel(__('courses.generate_lessons'));
    }

    protected static function createLessonsForCourse(Course $course, int $count, string $locale): int
    {
        $lessonTemplates = self::getLessonTemplates();

        // Get the current maximum sort order
        $maxSortOrder = $course->lessons()->max('sort_order') ?? 0;

        $createdCount = 0;

        for ($i = 0; $i < $count; $i++) {
            $template = $lessonTemplates[$i % count($lessonTemplates)];

            $lesson = Lesson::create([
                'course_id' => $course->id,
                'video_url' => $template['video_url'],
                'sort_order' => $maxSortOrder + $i + 1,
                'attachments' => [],
            ]);

            // Add translation for the specified locale only
            $lesson->translateOrNew($locale)->title = $template['title'] ?? '';
            $lesson->translateOrNew($locale)->description = $template['description'] ?? '';

            $lesson->save();
            $createdCount++;
        }

        return $createdCount;
    }

    protected static function getLessonTemplates(): array
    {
        return [
            [
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => __('courses.template_intro_title'),
                'description' => __('courses.template_intro_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=3JZ_D3ELwOQ',
                'title' => __('courses.template_started_title'),
                'description' => __('courses.template_started_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                'title' => __('courses.template_concepts_title'),
                'description' => __('courses.template_concepts_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=oHg5SJYRHA0',
                'title' => __('courses.template_practical_title'),
                'description' => __('courses.template_practical_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=yPYZpwSpKmA',
                'title' => __('courses.template_advanced_title'),
                'description' => __('courses.template_advanced_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=L_jWHffIx5E',
                'title' => __('courses.template_best_practices_title'),
                'description' => __('courses.template_best_practices_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=fJ9rUzIMcZQ',
                'title' => __('courses.template_pitfalls_title'),
                'description' => __('courses.template_pitfalls_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=QH2-TGUlwu4',
                'title' => __('courses.template_final_title'),
                'description' => __('courses.template_final_desc'),
            ],
        ];
    }
}
