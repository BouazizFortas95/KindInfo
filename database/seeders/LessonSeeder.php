<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fakerAr = Faker::create('ar_SA');
        $fakerEn = Faker::create('en_US');
        $fakerFr = Faker::create('fr_FR');

        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->warn('No courses found. Please seed courses first!');
            return;
        }

        $sampleVideos = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Rick Roll for testing
            'https://www.youtube.com/watch?v=jNQXAC9IVRw', // Me at the zoo
            'https://www.youtube.com/watch?v=9bZkp7q19f0', // Gangnam Style
        ];

        foreach ($courses as $course) {
            // Create 5-8 lessons per course
            $lessonCount = rand(5, 8);

            foreach (range(1, $lessonCount) as $i) {
                $lesson = Lesson::create([
                    'course_id' => $course->id,
                    'sort_order' => $i,
                    'video_url' => $sampleVideos[array_rand($sampleVideos)],
                    'attachments' => rand(0, 100) > 70 ? [
                        'lessons/sample-document-' . $i . '.pdf',
                        'lessons/sample-slides-' . $i . '.pptx'
                    ] : [],
                ]);

                $lesson->translateOrNew('ar')->fill([
                    'title' => 'الدرس ' . $i . ': ' . $fakerAr->realText(30),
                    'description' => $fakerAr->realText(150),
                ]);

                $lesson->translateOrNew('en')->fill([
                    'title' => 'Lesson ' . $i . ': ' . $fakerEn->sentence(4),
                    'description' => $fakerEn->paragraph(2),
                ]);

                $lesson->translateOrNew('fr')->fill([
                    'title' => 'Leçon ' . $i . ': ' . $fakerFr->sentence(4),
                    'description' => $fakerFr->paragraph(2),
                ]);

                $lesson->save();
            }
        }

        $this->command->info('Lessons seeded successfully for all courses!');
    }
}