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
            [
                'en' => ['name' => 'Home & Garden', 'slug' => 'home-garden'],
                'ar' => ['name' => 'المنزل والحديقة', 'slug' => 'home-garden-ar'],
                'fr' => ['name' => 'Maison et Jardin', 'slug' => 'maison-jardin'],
                'children' => [
                    [
                        'en' => ['name' => 'Furniture', 'slug' => 'furniture'],
                        'ar' => ['name' => 'الأثاث', 'slug' => 'furniture-ar'],
                        'fr' => ['name' => 'Meubles', 'slug' => 'meubles'],
                    ],
                    [
                        'en' => ['name' => 'Kitchenware', 'slug' => 'kitchenware'],
                        'ar' => ['name' => 'أدوات المطبخ', 'slug' => 'kitchenware-ar'],
                        'fr' => ['name' => 'Ustensiles de cuisine', 'slug' => 'ustensiles-cuisine'],
                    ],
                ]
            ]
        ];

        foreach ($mainCategories as $catData) {
            $children = $catData['children'] ?? [];

            $cat = new Category();
            foreach (['en', 'ar', 'fr'] as $locale) {
                if (isset($catData[$locale])) {
                    $cat->translateOrNew($locale)->name = $catData[$locale]['name'];
                    $cat->translateOrNew($locale)->slug = $catData[$locale]['slug'];
                }
            }
            $cat->save();

            foreach ($children as $childData) {
                $child = new Category();
                $child->parent_id = $cat->id;
                foreach (['en', 'ar', 'fr'] as $locale) {
                    if (isset($childData[$locale])) {
                        $child->translateOrNew($locale)->name = $childData[$locale]['name'];
                        $child->translateOrNew($locale)->slug = $childData[$locale]['slug'];
                    }
                }
                $child->save();
            }
        }
    }
}
