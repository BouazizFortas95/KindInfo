<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class LessonsSeeder extends Seeder
{
    /**
     * Run the database seeder to create demo lessons for all courses using the current application locale.
     */
    public function run(): void
    {
        $locale = App::getLocale();

        // Sample lesson templates using localization keys
        $lessonTemplates = [
            [
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'sort_order' => 1,
                'title' => __('courses.template_intro_title'),
                'description' => __('courses.template_intro_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=3JZ_D3ELwOQ',
                'sort_order' => 2,
                'title' => __('courses.template_started_title'),
                'description' => __('courses.template_started_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                'sort_order' => 3,
                'title' => __('courses.template_concepts_title'),
                'description' => __('courses.template_concepts_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=oHg5SJYRHA0',
                'sort_order' => 4,
                'title' => __('courses.template_practical_title'),
                'description' => __('courses.template_practical_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=yPYZpwSpKmA',
                'sort_order' => 5,
                'title' => __('courses.template_advanced_title'),
                'description' => __('courses.template_advanced_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=L_jWHffIx5E',
                'sort_order' => 6,
                'title' => __('courses.template_best_practices_title'),
                'description' => __('courses.template_best_practices_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=fJ9rUzIMcZQ',
                'sort_order' => 7,
                'title' => __('courses.template_pitfalls_title'),
                'description' => __('courses.template_pitfalls_desc'),
            ],
            [
                'video_url' => 'https://www.youtube.com/watch?v=QH2-TGUlwu4',
                'sort_order' => 8,
                'title' => __('courses.template_final_title'),
                'description' => __('courses.template_final_desc'),
            ],
        ];

        // Get all courses
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->warn('No courses found. Please create courses first.');
            return;
        }

        foreach ($courses as $course) {
            $this->command->info("Creating lessons for course: {$course->title} (Locale: {$locale})");

            foreach ($lessonTemplates as $template) {
                $lesson = Lesson::create([
                    'course_id' => $course->id,
                    'video_url' => $template['video_url'],
                    'sort_order' => $template['sort_order'],
                    'attachments' => [],
                ]);

                // Add translation for the current application locale only
                $lesson->translateOrNew($locale)->title = $template['title'];
                $lesson->translateOrNew($locale)->description = $template['description'];

                $lesson->save();
            }

            $this->command->info("Created " . count($lessonTemplates) . " lessons for course: {$course->title}");
        }

        $this->command->info('✓ Lessons seeded successfully using locale: ' . $locale);
    }
}
