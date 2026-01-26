<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $fakerAr = Faker::create('ar_SA');
        $fakerEn = Faker::create('en_US');
        $fakerFr = Faker::create('fr_FR');

        $categoryIds = Category::pluck('id');

        if ($categoryIds->isEmpty()) {
            $this->command->warn('No categories found. Please seed categories first!');
            return;
        }

        foreach (range(1, 50) as $i) {

            $course = Course::create([
                'category_id' => $categoryIds->random(),
                'price' => rand(100, 1000),
                'is_active' => true,
                'thumbnail' => 'courses/thumbnail-' . $i . '.jpg',
            ]);

            $course->translateOrNew('ar')->fill([
                'title' => 'دورة ' . $fakerAr->realText(20),
                'description' => $fakerAr->realText(200),
                'slug' => 'course-ar-' . $i,
            ]);

            $course->translateOrNew('en')->fill([
                'title' => $fakerEn->sentence(3),
                'description' => $fakerEn->paragraph(3),
                'slug' => 'course-en-' . $i,
            ]);

            $course->translateOrNew('fr')->fill([
                'title' => $fakerFr->sentence(3),
                'description' => $fakerFr->paragraph(3),
                'slug' => 'course-fr-' . $i,
            ]);

            $course->save();
        }

        $this->command->info('50 Courses seeded successfully with translations and categories!');
    }
}
