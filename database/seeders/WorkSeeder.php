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

            $work = new Work();
            $work->slug_main = Str::slug($titleEn) . '-' . $i;
            $work->is_published = $faker->boolean(80);
            $work->feature_image = 'works/demo-' . $i . '.jpg';

            // English translation
            $work->translateOrNew('en')->title = $titleEn;
            $work->translateOrNew('en')->slug = Str::slug($titleEn);
            $work->translateOrNew('en')->description = $faker->paragraph(2);
            $work->translateOrNew('en')->content = $faker->text(500);

            // Arabic translation
            $work->translateOrNew('ar')->title = $titleAr;
            $work->translateOrNew('ar')->slug = 'عمل-' . $i;
            $work->translateOrNew('ar')->description = 'هذا وصف قصير للعمل باللغة العربية رقم ' . $i;
            $work->translateOrNew('ar')->content = 'هذا محتوى العمل باللغة العربية مطول رقم ' . $i . '. ' . $faker->text(300);

            $work->save();

            // Assign 1-3 random categories
            $work->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
