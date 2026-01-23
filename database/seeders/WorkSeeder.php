<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Work;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        for ($i = 1; $i <= 10; $i++) {
            $titleEn = $faker->sentence(3);
            $titleAr = "عمل رقم " . $i . " - " . $faker->word();

            $work = Work::create([
                'slug_main' => Str::slug($titleEn) . '-' . $i,
                'is_published' => $faker->boolean(80),
                'feature_image' => 'works/demo-' . $i . '.jpg',
            ]);

            // English translation
            $work->translations()->create([
                'locale' => 'en',
                'title' => $titleEn,
                'slug' => Str::slug($titleEn),
                'description' => $faker->paragraph(2),
                'content' => $faker->text(500),
            ]);

            // Arabic translation
            $work->translations()->create([
                'locale' => 'ar',
                'title' => $titleAr,
                'slug' => 'عمل-' . $i,
                'description' => 'هذا وصف قصير للعمل باللغة العربية رقم ' . $i,
                'content' => 'هذا محتوى العمل باللغة العربية مطول رقم ' . $i . '. ' . $faker->text(300),
            ]);

            // Assign 1-3 random categories
            if ($categories->isNotEmpty()) {
                $work->categories()->attach(
                    $categories->random(min(rand(1, 3), $categories->count()))->pluck('id')->toArray()
                );
            }
        }
    }
}
