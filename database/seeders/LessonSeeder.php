<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== GENERATING LESSONS FOR ALL COURSES ===\n";

        // Clear existing lessons
        DB::table('lesson_translations')->delete();
        DB::table('lessons')->delete();
        
        $courses = Course::all();
        
        $lessonTemplates = [
            [
                'en' => [
                    'title' => 'Introduction and Overview',
                    'description' => 'Welcome to the course! This lesson covers the basic concepts and what you will learn throughout the course.'
                ],
                'ar' => [
                    'title' => 'مقدمة ونظرة عامة',
                    'description' => 'مرحباً بكم في الدورة! تغطي هذه الدرس المفاهيم الأساسية وما ستتعلمونه خلال الدورة.'
                ]
            ],
            [
                'en' => [
                    'title' => 'Getting Started',
                    'description' => 'Learn the fundamental concepts and prepare your learning environment for the best experience.'
                ],
                'ar' => [
                    'title' => 'البدء',
                    'description' => 'تعلم المفاهيم الأساسية وجهز بيئة التعلم الخاصة بك للحصول على أفضل تجربة.'
                ]
            ],
            [
                'en' => [
                    'title' => 'Core Concepts',
                    'description' => 'Dive deep into the main topics and understand the core principles that will guide your learning journey.'
                ],
                'ar' => [
                    'title' => 'المفاهيم الأساسية',
                    'description' => 'اغوص عميقاً في المواضيع الرئيسية وافهم المبادئ الأساسية التي ستوجه رحلة تعلمك.'
                ]
            ],
            [
                'en' => [
                    'title' => 'Practical Applications',
                    'description' => 'Apply what you have learned through hands-on exercises and real-world examples.'
                ],
                'ar' => [
                    'title' => 'التطبيقات العملية',
                    'description' => 'طبق ما تعلمته من خلال التمارين العملية والأمثلة الواقعية.'
                ]
            ],
            [
                'en' => [
                    'title' => 'Advanced Techniques',
                    'description' => 'Explore advanced methods and techniques to master the subject matter completely.'
                ],
                'ar' => [
                    'title' => 'التقنيات المتقدمة',
                    'description' => 'استكشف الطرق والتقنيات المتقدمة لإتقان الموضوع بشكل كامل.'
                ]
            ],
            [
                'en' => [
                    'title' => 'Best Practices',
                    'description' => 'Learn industry best practices and professional approaches to ensure quality results.'
                ],
                'ar' => [
                    'title' => 'أفضل الممارسات',
                    'description' => 'تعلم أفضل الممارسات في الصناعة والأساليب المهنية لضمان نتائج عالية الجودة.'
                ]
            ],
            [
                'en' => [
                    'title' => 'Troubleshooting and Debugging',
                    'description' => 'Common problems and their solutions. Learn how to identify and resolve issues effectively.'
                ],
                'ar' => [
                    'title' => 'استكشاف الأخطاء وإصلاحها',
                    'description' => 'المشاكل الشائعة وحلولها. تعلم كيفية تحديد المشاكل وحلها بفعالية.'
                ]
            ],
            [
                'en' => [
                    'title' => 'Project Workshop',
                    'description' => 'Work on a complete project that combines all the concepts learned in previous lessons.'
                ],
                'ar' => [
                    'title' => 'ورشة المشروع',
                    'description' => 'اعمل على مشروع متكامل يجمع كل المفاهيم المتعلمة في الدروس السابقة.'
                ]
            ],
            [
                'en' => [
                    'title' => 'Final Review and Assessment',
                    'description' => 'Review all key concepts and evaluate your understanding through comprehensive assessment.'
                ],
                'ar' => [
                    'title' => 'المراجعة النهائية والتقييم',
                    'description' => 'راجع جميع المفاهيم الأساسية وقيّم فهمك من خلال تقييم شامل.'
                ]
            ]
        ];

        $videoUrls = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://www.youtube.com/watch?v=oHg5SJYRHA0',
            'https://www.youtube.com/watch?v=SQoA_wjmE9w',
            'https://vimeo.com/147999374',
            'https://vimeo.com/76979871',
            'https://www.youtube.com/watch?v=ZZ5LpwO-An4',
            'https://www.youtube.com/watch?v=hFZFjoX2cGg',
            'https://vimeo.com/90312569',
            'https://www.youtube.com/watch?v=9bZkp7q19f0'
        ];

        foreach ($courses as $course) {
            echo "Creating lessons for course: {$course->title}\n";
            
            // Generate 5-9 lessons per course for variety
            $lessonsCount = rand(5, 9);
            
            for ($i = 0; $i < $lessonsCount; $i++) {
                $templateIndex = $i % count($lessonTemplates);
                $template = $lessonTemplates[$templateIndex];
                
                $lesson = Lesson::create([
                    'course_id' => $course->id,
                    'video_url' => $videoUrls[array_rand($videoUrls)],
                    'sort_order' => $i + 1,
                    'attachments' => $this->generateAttachments()
                ]);

                // Add translations
                foreach ($template as $locale => $translation) {
                    DB::table('lesson_translations')->insert([
                        'lesson_id' => $lesson->id,
                        'locale' => $locale,
                        'title' => $translation['title'] . ' - Part ' . ($i + 1),
                        'description' => $translation['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                echo "  + Lesson " . ($i + 1) . ": {$template['en']['title']}\n";
            }
            
            echo "  Created " . $lessonsCount . " lessons for course #" . $course->id . "\n\n";
        }

        $totalLessons = Lesson::count();
        echo "✅ Successfully created " . $totalLessons . " lessons for " . $courses->count() . " courses!\n";
    }

    private function generateAttachments(): array
    {
        $attachmentTypes = ['pdf', 'doc', 'ppt', 'zip', 'txt'];
        $attachments = [];
        
        // Generate 0-3 random attachments per lesson
        $count = rand(0, 3);
        
        for ($i = 0; $i < $count; $i++) {
            $type = $attachmentTypes[array_rand($attachmentTypes)];
            $attachments[] = [
                'name' => 'lesson_material_' . ($i + 1) . '.' . $type,
                'path' => 'attachments/lessons/lesson_material_' . ($i + 1) . '.' . $type,
                'size' => rand(1024, 10485760), // 1KB to 10MB
                'type' => $type
            ];
        }
        
        return $attachments;
    }
}