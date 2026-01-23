<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::query()->delete();

        $mainCategories = [
            [
                'en' => ['name' => 'Electronics', 'slug' => 'electronics'],
                'ar' => ['name' => 'الإلكترونيات', 'slug' => 'electronics-ar'],
                'fr' => ['name' => 'Électronique', 'slug' => 'electronique'],
                'children' => [
                    [
                        'en' => ['name' => 'Smartphones', 'slug' => 'smartphones'],
                        'ar' => ['name' => 'الهواتف الذكية', 'slug' => 'smartphones-ar'],
                        'fr' => ['name' => 'Smartphones', 'slug' => 'smartphones-fr'],
                    ],
                    [
                        'en' => ['name' => 'Laptops', 'slug' => 'laptops'],
                        'ar' => ['name' => 'أجهزة الكمبيوتر المحمول', 'slug' => 'laptops-ar'],
                        'fr' => ['name' => 'Ordinateurs portables', 'slug' => 'ordinateurs-portables'],
                    ],
                ]
            ],
            [
                'en' => ['name' => 'Clothing', 'slug' => 'clothing'],
                'ar' => ['name' => 'الملابس', 'slug' => 'clothing-ar'],
                'fr' => ['name' => 'Vêtements', 'slug' => 'vetements'],
                'children' => [
                    [
                        'en' => ['name' => 'Mens Fashion', 'slug' => 'mens-fashion'],
                        'ar' => ['name' => 'أزياء رجالية', 'slug' => 'mens-fashion-ar'],
                        'fr' => ['name' => 'Mode homme', 'slug' => 'mode-homme'],
                    ],
                    [
                        'en' => ['name' => 'Womens Fashion', 'slug' => 'womens-fashion'],
                        'ar' => ['name' => 'أزياء نسائية', 'slug' => 'womens-fashion-ar'],
                        'fr' => ['name' => 'Mode femme', 'slug' => 'mode-femme'],
                    ],
                ]
            ],
        ];

        foreach ($mainCategories as $catData) {
            $cat = Category::create([]);

            foreach (['en', 'ar', 'fr'] as $locale) {
                if (isset($catData[$locale])) {
                    $cat->translations()->create(array_merge(
                        ['locale' => $locale],
                        $catData[$locale]
                    ));
                }
            }

            foreach ($catData['children'] ?? [] as $childData) {
                $child = Category::create(['parent_id' => $cat->id]);

                foreach (['en', 'ar', 'fr'] as $locale) {
                    if (isset($childData[$locale])) {
                        $child->translations()->create(array_merge(
                            ['locale' => $locale],
                            $childData[$locale]
                        ));
                    }
                }
            }
        }
    }
}
