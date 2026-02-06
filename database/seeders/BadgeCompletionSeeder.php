<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BadgeCompletionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create specific Course Completion Badges
        $badges = [
            [
                'type' => 'course_completion',
                'points_required' => 0,
                'is_active' => true,
                'icon_path' => 'badges/laravel-mastery.png', // Placeholder
                'translations' => ['en' => ['name' => 'Laravel Mastery', 'description' => 'Mastered Laravel Framework'], 'ar' => ['name' => 'إتقان لارافيل', 'description' => 'أتقن إطار عمل لارافيل']]
            ],
            [
                'type' => 'course_completion',
                'points_required' => 0,
                'is_active' => true,
                'icon_path' => 'badges/php-expert.png',
                'translations' => ['en' => ['name' => 'PHP Expert', 'description' => 'Expert knowledge in PHP'], 'ar' => ['name' => 'خبير PHP', 'description' => 'معرفة خبيرة في PHP']]
            ],
            [
                'type' => 'course_completion',
                'points_required' => 0,
                'is_active' => true,
                'icon_path' => 'badges/js-ninja.png',
                'translations' => ['en' => ['name' => 'JS Ninja', 'description' => 'Completed JavaScript Advanced Course'], 'ar' => ['name' => 'نينجا الجافاسكريبت', 'description' => 'أتم دورة الجافاسكريبت المتقدمة']]
            ],
            [
                'type' => 'course_completion',
                'points_required' => 0,
                'is_active' => true,
                'icon_path' => 'badges/ui-ux-designer.png',
                'translations' => ['en' => ['name' => 'UI/UX Designer', 'description' => 'Completed UI/UX Design Fundamentals'], 'ar' => ['name' => 'مصمم واجهة وتجربة مستخدم', 'description' => 'أتم أساسيات تصميم واجهة وتجربة المستخدم']]
            ],
            [
                'type' => 'course_completion',
                'points_required' => 0,
                'is_active' => true,
                'icon_path' => 'badges/devops-engineer.png',
                'translations' => ['en' => ['name' => 'DevOps Engineer', 'description' => 'Completed DevOps Essentials'], 'ar' => ['name' => 'مهندس ديف أوبس', 'description' => 'أتم أساسيات الديف أوبس']]
            ]
        ];

        $createdBadges = collect();

        foreach ($badges as $badgeData) {
            $translations = $badgeData['translations'];
            unset($badgeData['translations']);

            $badge = Badge::create($badgeData);
            foreach ($translations as $locale => $trans) {
                $badge->translateOrNew($locale)->fill($trans);
            }
            $badge->save();
            $createdBadges->push($badge);
            $this->command->info("Created Badge: {$badge->translate('en')->name}");
        }

        // 2. Assign Badges to Courses
        // Get all courses
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->warn("No courses found to assign badges to.");
            return;
        }

        // Distribute created badges among courses
        foreach ($courses as $index => $course) {
            // Assign a random badge from the ones we just created, or leave null occasionally
            if ($index % 2 == 0) { // Assign to every 2nd course for variety
                $randomBadge = $createdBadges->random();
                $course->badge_id = $randomBadge->id;
                $course->save();
                $courseName = $course->translate('en')?->title ?? 'Unknown Course';
                $badgeName = $randomBadge->translate('en')?->name ?? 'Badge';
                $this->command->info("Assigned '{$badgeName}' to course '{$courseName}'");
            }
        }

        // 3. Simulate Course Completion for Admin User
        $user = User::where('email', 'admin@admin.com')->first();

        if (!$user) {
            $user = User::first();
        }

        if (!$user) {
            $this->command->warn("No user found to simulate completion.");
            return;
        }

        // Find courses with badges that we just assigned
        $coursesWithBadges = Course::whereNotNull('badge_id')->take(3)->get(); // Users completes 3 courses

        foreach ($coursesWithBadges as $course) {
            $courseTitleEn = $course->translate('en')?->title ?? 'Unknown Course';
            $courseTitleAr = $course->translate('ar')?->title ?? 'دورة غير معروفة';

            $this->command->info("Simulating completion for Course: {$courseTitleEn}");

            // A. Mark all lessons as complete
            $lessons = $course->lessons;
            foreach ($lessons as $lesson) {
                if (!$user->lessons()->where('lesson_id', $lesson->id)->exists()) {
                    $user->lessons()->attach($lesson->id, [
                        'progress' => 100,
                        'last_watched_at' => now()->subDays(rand(1, 30)), // Random date in last month
                    ]);
                }
            }

            // B. Award Badge
            if (!$user->badges()->where('badge_id', $course->badge_id)->exists()) {
                $earnedDate = now()->subDays(rand(1, 7)); // Earned recently
                $user->badges()->attach($course->badge_id, ['earned_at' => $earnedDate]);
                $badgeName = $course->badge->translate('en')?->name ?? 'Badge';
                $this->command->info(" - Badge Awarded: {$badgeName} on {$earnedDate->toDateString()}");
            }

            // C. Issue Certificate
            if (!Certificate::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                $issuedDate = now()->subDays(rand(1, 7));
                $certificate = Certificate::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'certificate_uuid' => Str::uuid(),
                    'issued_at' => $issuedDate,
                ]);
                
                // Set translations properly
                $certificate->translateOrNew('en')->title = 'Certificate of Completion';
                $certificate->translateOrNew('en')->body = "This certifies that {$user->name} has successfully completed the course {$courseTitleEn}.";
                $certificate->translateOrNew('ar')->title = 'شهادة إتمام';
                $certificate->translateOrNew('ar')->body = "تشهد هذه الوثيقة بأن {$user->name} قد أتم بنجاح دورة {$courseTitleAr}.";
                $certificate->save();
                
                $this->command->info(" - Certificate Issued on {$issuedDate->toDateString()}");
            }
        }
    }
}
