<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonUserSeeder extends Seeder
{
    /**
     * Seed lesson tracking/completion records for users.
     * This generates progress tracking data for all users across all lessons.
     */
    public function run(): void
    {
        $users = User::all();
        $lessons = Lesson::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please create users first.');
            return;
        }

        if ($lessons->isEmpty()) {
            $this->command->warn('No lessons found. Please create lessons first.');
            return;
        }

        $this->command->info('Generating lesson tracking records...');

        $progressOptions = [
            ['progress' => 0, 'weight' => 20],      // Not started
            ['progress' => 25, 'weight' => 15],     // Just started
            ['progress' => 50, 'weight' => 15],     // Half way
            ['progress' => 75, 'weight' => 20],     // Almost done
            ['progress' => 100, 'weight' => 30],    // Completed
        ];

        $totalRecords = 0;

        foreach ($users as $user) {
            // Each user will have progress on a random subset of lessons (30-80%)
            $lessonCount = $lessons->count();
            $userLessonCount = rand((int) ($lessonCount * 0.3), (int) ($lessonCount * 0.8));

            $userLessons = $lessons->random(min($userLessonCount, $lessonCount));

            foreach ($userLessons as $lesson) {
                // Weighted random selection of progress
                $progress = $this->getWeightedRandomProgress($progressOptions);

                // Only create records if there's some progress or randomly for variety
                if ($progress > 0 || rand(1, 100) <= 30) {
                    DB::table('lesson_user')->insert([
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                        'progress' => $progress,
                        'last_watched_at' => $progress > 0
                            ? now()->subDays(rand(0, 30))->subHours(rand(0, 23))
                            : null,
                        'created_at' => now()->subDays(rand(1, 60)),
                        'updated_at' => now()->subDays(rand(0, 7)),
                    ]);

                    $totalRecords++;
                }
            }

            $this->command->info("Created tracking records for user: {$user->name}");
        }

        $this->command->info("✓ Created {$totalRecords} lesson tracking records for {$users->count()} users!");
    }

    /**
     * Get a weighted random progress value
     */
    protected function getWeightedRandomProgress(array $options): int
    {
        $totalWeight = array_sum(array_column($options, 'weight'));
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($options as $option) {
            $currentWeight += $option['weight'];
            if ($random <= $currentWeight) {
                return $option['progress'];
            }
        }

        return 0;
    }
}
