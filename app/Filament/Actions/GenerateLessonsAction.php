<?php

namespace App\Filament\Actions;

use App\Models\Course;
use App\Models\Lesson;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class GenerateLessonsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generate_lessons';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $locale = App::getLocale();

        $this->label(__('courses.generate_lessons'))
            ->icon('heroicon-o-academic-cap')
            ->color('success')
            ->form([
                Select::make('course_id')
                    ->label(__('courses.select_course'))
                    ->options(Course::all()->mapWithKeys(fn($course) => [$course->id => $course->title]))
                    ->required()
                    ->searchable()
                    ->columnSpanFull(),

                Repeater::make('lesson_templates')
                    ->label(__('courses.lesson_templates') . " (" . strtoupper($locale) . ")")
                    ->schema([
                        TextInput::make('video_url')
                            ->label(__('courses.video_url'))
                            ->url()
                            ->required()
                            ->placeholder('https://www.youtube.com/watch?v=...'),

                        TextInput::make('title')
                            ->label(__('courses.title'))
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label(__('courses.description'))
                            ->rows(3),
                    ])
                    ->columns(1)
                    ->defaultItems(1)
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['title'] ?? __('courses.new_lesson'))
                    ->addActionLabel(__('courses.add_lesson'))
                    ->columnSpanFull(),
            ])
            ->action(function (array $data): void {
                DB::transaction(function () use ($data) {
                    $locale = App::getLocale();
                    $course = Course::findOrFail($data['course_id']);
                    $lessonTemplates = $data['lesson_templates'] ?? [];

                    // Get the current maximum sort order
                    $maxSortOrder = $course->lessons()->max('sort_order') ?? 0;

                    $createdCount = 0;

                    foreach ($lessonTemplates as $index => $template) {
                        $lesson = Lesson::create([
                            'course_id' => $course->id,
                            'video_url' => $template['video_url'],
                            'sort_order' => $maxSortOrder + $index + 1,
                            'attachments' => [],
                        ]);

                        // Add translation for the current application locale only
                        $lesson->translateOrNew($locale)->title = $template['title'] ?? '';
                        $lesson->translateOrNew($locale)->description = $template['description'] ?? '';

                        $lesson->save();
                        $createdCount++;
                    }

                    Notification::make()
                        ->title(__('general.success'))
                        ->body(__('courses.success_created', [
                            'count' => $createdCount,
                            'title' => $course->title,
                        ]))
                        ->success()
                        ->send();
                });
            })
            ->modalWidth('4xl')
            ->modalHeading(__('courses.generate_lessons'))
            ->modalSubmitActionLabel(__('courses.generate_lessons'));
    }
}
