<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Course;
use App\Models\Lesson;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManageCourses extends ManageRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('5xl')
                ->using(function (array $data): Course {
                    return DB::transaction(function () use ($data) {
                        $locale = App::getLocale();

                        // Create course with main data
                        $record = new Course();
                        $record->category_id = $data['category_id'] ?? null;
                        $record->thumbnail = $data['thumbnail'] ?? null;
                        $record->price = $data['price'] ?? 0;
                        $record->is_active = $data['is_active'] ?? true;


                        // Set course translation for current locale
                        $record->translateOrNew($locale)->title = $data['title'] ?? '';
                        $record->translateOrNew($locale)->slug = Str::slug($data['title']) ?? '';
                        $record->translateOrNew($locale)->description = $data['description'] ?? null;

                        $record->save();

                        // Handle lessons
                        $lessonsData = $data['lessons'] ?? [];

                        foreach ($lessonsData as $index => $lessonData) {
                            $lesson = new Lesson();
                            $lesson->course_id = $record->id;
                            $lesson->video_url = $lessonData['video_url'] ?? '';
                            $lesson->sort_order = $index;

                            // Set lesson translation for current locale
                            $lesson->translateOrNew($locale)->title = $lessonData['title'] ?? '';
                            $lesson->translateOrNew($locale)->description = $lessonData['description'] ?? null;

                            $lesson->save();
                        }

                        return $record;
                    });
                }),
        ];
    }
}
