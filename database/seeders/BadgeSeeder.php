<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'type' => 'course_completion',
                'points_required' => 0,
                'is_active' => true,
                'translations' => [
                    'en' => [
                        'name' => 'Course Completion',
                        'description' => 'Awarded for completing a course',
                    ],
                    'ar' => [
                        'name' => 'إتمام الدورة',
                        'description' => 'يُمنح عند إتمام دورة',
                    ],
                ]
            ],
            [
                'type' => 'points_achievement',
                'points_required' => 100,
                'is_active' => true,
                'translations' => [
                    'en' => [
                        'name' => 'Bronze Badge',
                        'description' => 'Awarded for earning 100 points',
                    ],
                    'ar' => [
                        'name' => 'شارة البرونز',
                        'description' => 'يُمنح عند كسب 100 نقطة',
                    ],
                ]
            ],
            [
                'type' => 'points_achievement',
                'points_required' => 500,
                'is_active' => true,
                'translations' => [
                    'en' => [
                        'name' => 'Silver Badge',
                        'description' => 'Awarded for earning 500 points',
                    ],
                    'ar' => [
                        'name' => 'شارة الفضة',
                        'description' => 'يُمنح عند كسب 500 نقطة',
                    ],
                ]
            ],
            [
                'type' => 'special_achievement',
                'points_required' => 0,
                'is_active' => true,
                'translations' => [
                    'en' => [
                        'name' => 'Early Bird',
                        'description' => 'Awarded for joining in the first month',
                    ],
                    'ar' => [
                        'name' => 'العضو المبكر',
                        'description' => 'يُمنح للانضمام في الشهر الأول',
                    ],
                ]
            ],
        ];

        foreach ($badges as $badgeData) {
            // Skip if badge already exists with same type and points
            $existing = Badge::where('type', $badgeData['type'])
                            ->where('points_required', $badgeData['points_required'])
                            ->first();
            
            if ($existing) {
                echo "Badge already exists: {$badgeData['type']} with {$badgeData['points_required']} points\n";
                continue;
            }

            $translations = $badgeData['translations'];
            unset($badgeData['translations']);

            $badge = Badge::create($badgeData);
            echo "Created badge: {$badge->id} - {$badge->type}\n";

            foreach ($translations as $locale => $translation) {
                $badge->translateOrNew($locale)->fill($translation);
                echo "  Added translation: {$locale} - {$translation['name']}\n";
            }

            $badge->save();
            echo "  Saved badge with translations\n";
        }
    }
}