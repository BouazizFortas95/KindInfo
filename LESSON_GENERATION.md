# Lesson Generation System

This document explains the four comprehensive solutions for generating, managing, and tracking lessons across all courses in your Laravel application.

## Table of Contents
1. [Database Seeder](#1-database-seeder)
2. [Filament Action for Single Course](#2-filament-action-for-single-course)
3. [Bulk Action for Multiple Courses](#3-bulk-action-for-multiple-courses)
4. [Lesson Tracking Seeder](#4-lesson-tracking-seeder)

---

## 1. Database Seeder

**File:** `database/seeders/LessonsSeeder.php`

### Purpose
Creates sample/demo lessons for all existing courses with full multilingual support (English, French, Arabic).

### Features
- Automatically generates 8 standardized lessons for each course
- Full translations in all three supported locales
- Realistic lesson structure with titles, descriptions, and video URLs
- Proper sort ordering

### Usage

```bash
# Run the seeder
php artisan db:seed --class=LessonsSeeder

# Or include it in DatabaseSeeder.php
```

### Lesson Templates Included
1. Introduction to the Course
2. Getting Started
3. Core Concepts
4. Practical Applications
5. Advanced Techniques
6. Best Practices
7. Common Pitfalls
8. Final Project

---

## 2. Filament Action for Single Course

**File:** `app/Filament/Actions/GenerateLessonsAction.php`

### Purpose
Provides a Filament action in the admin panel to generate custom lessons for a specific course through a user-friendly modal form.

### Features
- Select target course from dropdown
- Add multiple lesson templates via repeater
- Input translations for all three locales (en, fr, ar)
- Reorderable lessons
- Collapsible form for better UX

### Usage
1. Navigate to the Courses page in Filament admin
2. Click the "Generate Lessons" button in the header
3. Select the course
4. Fill in lesson details for each locale
5. Submit to create lessons

### Integration
The action is added to the ManageCourses page header actions:

```php
GenerateLessonsAction::make()
```

---

## 3. Bulk Action for Multiple Courses

**File:** `app/Filament/Actions/BulkGenerateLessonsAction.php`

### Purpose
Allows bulk generation of lessons for multiple selected courses simultaneously using predefined templates.

### Features
- Select multiple courses from the table
- Choose from predefined templates:
  - **Basic Course**: 5 lessons
  - **Intermediate Course**: 8 lessons
  - **Advanced Course**: 12 lessons
  - **Custom**: Specify your own number (1-20)
- Automated multilingual content generation
- Progress notifications

### Usage
1. Navigate to the Courses table in Filament admin
2. Select one or more courses using checkboxes
3. Click "Generate Lessons" from the bulk actions toolbar
4. Choose a template
5. Submit to generate lessons for all selected courses

### Integration
The bulk action is added to the CourseResource table:

```php
->toolbarActions([
    BulkGenerateLessonsAction::make(),
    // ... other actions
])
```

---

## 4. Lesson Tracking Seeder

**File:** `database/seeders/LessonUserSeeder.php`

### Purpose
Generates realistic lesson tracking and completion records for all users across all lessons for testing and development.

### Features
- Creates progress records for 30-80% of lessons per user (randomized)
- Weighted progress distribution:
  - 0% (Not started): 20% probability
  - 25% (Just started): 15% probability
  - 50% (Half way): 15% probability
  - 75% (Almost done): 20% probability
  - 100% (Completed): 30% probability
- Realistic timestamps for `last_watched_at`
- Tracks creation and update times

### Usage

```bash
# Run the seeder
php artisan db:seed --class=LessonUserSeeder

# Or include it in DatabaseSeeder.php
```

### Database Table
The seeder populates the `lesson_user` pivot table with:
- `user_id`: Foreign key to users table
- `lesson_id`: Foreign key to lessons table
- `progress`: Integer (0-100)
- `last_watched_at`: Timestamp (nullable)
- `created_at` & `updated_at`: Timestamps

---

## Complete Workflow Example

### Scenario: Setting up a new development environment

```bash
# 1. Create courses with categories (manually or via seeder)

# 2. Generate lessons for all courses
php artisan db:seed --class=LessonsSeeder

# 3. Create users (manually or via seeder)

# 4. Generate lesson tracking data
php artisan db:seed --class=LessonUserSeeder
```

### Scenario: Adding lessons to a specific course in production

1. Log into Filament admin panel
2. Go to Courses page
3. Click "Generate Lessons" button
4. Select your course
5. Add custom lesson content with translations
6. Submit

### Scenario: Bulk updating multiple courses

1. Log into Filament admin panel
2. Go to Courses page
3. Select multiple courses (checkboxes)
4. Click "Generate Lessons" from toolbar
5. Choose template (e.g., "Intermediate Course - 8 lessons")
6. Submit

---

## Customization

### Modifying Lesson Templates

Edit the `getLessonTemplates()` method in:
- `LessonsSeeder.php` (for seeder)
- `BulkGenerateLessonsAction.php` (for bulk action)

```php
protected function getLessonTemplates(): array
{
    return [
        [
            'video_url' => 'YOUR_VIDEO_URL',
            'translations' => [
                'en' => [
                    'title' => 'Your Title',
                    'description' => 'Your Description',
                ],
                // Add other locales...
            ],
        ],
        // Add more templates...
    ];
}
```

### Adding New Locales

1. Update the locale arrays in all three files:
   ```php
   foreach (['en', 'fr', 'ar', 'de'] as $locale) { // Added 'de'
       // ...
   }
   ```

2. Add translations to each template

### Changing Progress Distribution

In `LessonUserSeeder.php`, modify the `$progressOptions` array:

```php
$progressOptions = [
    ['progress' => 0, 'weight' => 20],   // Adjust weight
    ['progress' => 50, 'weight' => 30],  // Adjust weight
    ['progress' => 100, 'weight' => 50], // Adjust weight
];
```

---

## Troubleshooting

### No courses found
**Error**: "No courses found. Please create courses first."
**Solution**: Create at least one course before running the LessonsSeeder.

### No users found
**Error**: "No users found. Please create users first."
**Solution**: Create users before running the LessonUserSeeder.

### Lessons not showing translations
**Issue**: Lessons show empty titles in different locales
**Solution**: Ensure the locale switcher is working and translations were saved properly.

### Duplicate lessons when using bulk action multiple times
**Expected behavior**: Each execution adds more lessons with incremented sort_order.
**Solution**: If you want to replace rather than add, manually delete existing lessons first.

---

## Advanced Features

### Attachments Support
The system supports file attachments for lessons. Simply add files to the `attachments` field when creating lessons manually through the Filament form.

### Sort Order Management
Lessons are automatically ordered using the `sort_order` field. The system:
- Preserves existing lesson order
- Appends new lessons to the end
- Allows manual reordering through the Filament repeater

### Progress Tracking
The `lesson_user` table stores:
- Progress percentage (0-100)
- Last watched timestamp
- Automatic timestamps for tracking changes

---

## API Integration (Future)

The current implementation is focused on admin panel and database seeding. For API integration:

1. Create API endpoints to fetch lessons
2. Implement progress tracking API
3. Add real-time progress updates
4. Sync with video player events

---

## Database Schema

### lessons table
```
id (bigint)
course_id (bigint, foreign key)
video_url (string)
sort_order (integer)
attachments (json/array)
created_at (timestamp)
updated_at (timestamp)
```

### lesson_translations table
```
id (bigint)
lesson_id (bigint, foreign key)
locale (string, indexed)
title (string)
description (text, nullable)
created_at (timestamp)
updated_at (timestamp)
UNIQUE(lesson_id, locale)
```

### lesson_user table (pivot)
```
id (bigint)
user_id (bigint, foreign key)
lesson_id (bigint, foreign key)
progress (integer, 0-100)
last_watched_at (timestamp, nullable)
created_at (timestamp)
updated_at (timestamp)
```

---

## Performance Considerations

### Large Datasets
- The seeders use batch processing
- Bulk actions use database transactions
- Consider adding queues for very large operations

### Optimization Tips
```php
// For large course sets, consider chunking
Course::chunk(50, function ($courses) {
    foreach ($courses as $course) {
        // Generate lessons
    }
});
```

---

## Testing

### Manual Testing Checklist
- [ ] Run LessonsSeeder and verify lessons are created
- [ ] Test GenerateLessonsAction in Filament
- [ ] Test BulkGenerateLessonsAction with multiple courses
- [ ] Run LessonUserSeeder and verify tracking records
- [ ] Switch locales and verify translations
- [ ] Check lesson ordering
- [ ] Verify progress tracking data

### Automated Testing Example
```php
public function test_lessons_seeder_creates_lessons()
{
    $course = Course::factory()->create();
    
    $this->artisan('db:seed', ['--class' => 'LessonsSeeder']);
    
    $this->assertDatabaseHas('lessons', [
        'course_id' => $course->id,
    ]);
}
```

---

## License & Credits
This lesson generation system was created for the KindInfo Laravel application using:
- Laravel 12
- Filament V3
- Astrotomic Laravel Translatable
