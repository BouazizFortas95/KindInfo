<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::query()->delete();

        $services = [
            [
                'icon' => 'heroicon-o-computer-desktop',
                'order' => 1,
                'en' => [
                    'title' => 'Web Development',
                    'slug' => 'web-development',
                    'description' => 'Custom website development using modern technologies like Laravel and Filament.',
                ],
                'ar' => [
                    'title' => 'تطوير المواقع',
                    'slug' => 'web-development-ar',
                    'description' => 'تطوير مواقع مخصصة باستخدام تقنيات حديثة مثل Laravel و Filament.',
                ],
                'fr' => [
                    'title' => 'Développement Web',
                    'slug' => 'developpement-web',
                    'description' => 'Développement de sites web personnalisés utilisant des technologies modernes comme Laravel et Filament.',
                ],
            ],
            [
                'icon' => 'heroicon-o-device-phone-mobile',
                'order' => 2,
                'en' => [
                    'title' => 'Mobile Apps',
                    'slug' => 'mobile-apps',
                    'description' => 'Native and cross-platform mobile application development for iOS and Android.',
                ],
                'ar' => [
                    'title' => 'تطبيقات الجوال',
                    'slug' => 'mobile-apps-ar',
                    'description' => 'تطوير تطبيقات الجوال الأصلية والمتعددة المنصات لنظامي iOS و Android.',
                ],
                'fr' => [
                    'title' => 'Applications Mobiles',
                    'slug' => 'applications-mobiles',
                    'description' => 'Développement d\'applications mobiles natives et multiplateformes pour iOS et Android.',
                ],
            ],
            [
                'icon' => 'heroicon-o-paint-brush',
                'order' => 3,
                'en' => [
                    'title' => 'UI/UX Design',
                    'slug' => 'ui-ux-design',
                    'description' => 'Beautiful and intuitive user interfaces designed for the best user experience.',
                ],
                'ar' => [
                    'title' => 'تصميم واجهة المستخدم',
                    'slug' => 'ui-ux-design-ar',
                    'description' => 'واجهات مستخدم جميلة وبديهية مصممة لأفضل تجربة مستخدم.',
                ],
                'fr' => [
                    'title' => 'Design UI/UX',
                    'slug' => 'design-ui-ux',
                    'description' => 'Interfaces utilisateur belles et intuitives conçues pour la meilleure expérience utilisateur.',
                ],
            ],
            [
                'icon' => 'heroicon-o-megaphone',
                'order' => 4,
                'en' => [
                    'title' => 'Digital Marketing',
                    'slug' => 'digital-marketing',
                    'description' => 'Strategic marketing campaigns to grow your brand and reach more customers.',
                ],
                'ar' => [
                    'title' => 'التسويق الرقمي',
                    'slug' => 'digital-marketing-ar',
                    'description' => 'حملات تسويقية استراتيجية لتنمية علامتك التجارية والوصول إلى المزيد من العملاء.',
                ],
                'fr' => [
                    'title' => 'Marketing Digital',
                    'slug' => 'marketing-digital',
                    'description' => 'Campagnes de marketing stratégique pour développer votre marque et atteindre plus de clients.',
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $service = Service::create([
                'icon' => $serviceData['icon'],
                'order' => $serviceData['order'],
                'is_active' => true,
            ]);

            foreach (['en', 'ar', 'fr'] as $locale) {
                if (isset($serviceData[$locale])) {
                    $service->translations()->create(array_merge(
                        ['locale' => $locale],
                        $serviceData[$locale]
                    ));
                }
            }
        }
    }
}
