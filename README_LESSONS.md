# Quick Start: Lesson Generation

## 🚀 Quick Commands

### 1. Generate Demo Lessons for All Courses
```bash
php artisan db:seed --class=LessonsSeeder
```
Creates 8 multilingual lessons for each course in your database.

### 2. Generate Lesson Tracking Data
```bash
php artisan db:seed --class=LessonUserSeeder
```
Creates realistic progress/completion records for all users.

### 3. Both at Once
```bash
php artisan db:seed --class=LessonsSeeder
php artisan db:seed --class=LessonUserSeeder
```

---

## 📋 Features Overview

| Feature | File | When to Use |
|---------|------|-------------|
| **Database Seeder** | `LessonsSeeder.php` | Development/Testing - bulk create lessons |
| **Filament Action** | `GenerateLessonsAction.php` | Admin panel - create custom lessons for ONE course |
| **Bulk Action** | `BulkGenerateLessonsAction.php` | Admin panel - create lessons for MULTIPLE courses |
| **Tracking Seeder** | `LessonUserSeeder.php` | Development/Testing - generate user progress data |

---

## 🎯 Use Cases

### For Development/Testing
1. Run `LessonsSeeder` to populate all courses with demo lessons
2. Run `LessonUserSeeder` to create realistic user progress

### In Production (via Admin Panel)

#### Add lessons to ONE course:
1. Go to **Courses** page
2. Click **"Generate Lessons"** button (top right)
3. Fill in custom lesson details
4. Submit

#### Add lessons to MULTIPLE courses:
1. Go to **Courses** page
2. ☑️ Select multiple courses (checkboxes)
3. Click **"Generate Lessons"** in toolbar
4. Choose template (Basic/Intermediate/Advanced/Custom)
5. Submit

---

## 📚 What Gets Created

### LessonsSeeder
- 8 lessons per course
- Translations: English, French, Arabic
- Includes: Introduction, Core Concepts, Practical Applications, Best Practices, etc.

### LessonUserSeeder
- Random progress (0%, 25%, 50%, 75%, 100%)
- Each user gets 30-80% of available lessons
- Realistic timestamps for `last_watched_at`

---

## 🔧 Customization

### Change Number of Lessons
Edit `LessonsSeeder.php` or choose "Custom" template in bulk action.

### Add New Locale
Update locale arrays in all three action files:
```php
foreach (['en', 'fr', 'ar', 'de'] as $locale) { // Added 'de'
```

### Modify Lesson Templates
Edit the `getLessonTemplates()` method in seeders/actions.

---

## ⚠️ Prerequisites

Before running seeders:
- ✅ Courses must exist
- ✅ Users must exist (for LessonUserSeeder)
- ✅ Database migrated
- ✅ Locales configured

---

## 📖 Full Documentation
See `LESSON_GENERATION.md` for complete documentation.

---

## 🐛 Troubleshooting

**"No courses found"** → Create courses first
**"No users found"** → Create users first  
**Translations not showing** → Check locale switcher and Astrotomic config
**Duplicate lessons** → Expected behavior - each run adds more lessons

---

## ✨ What's Multilingual?

All lesson content supports:
- 🇬🇧 English (en)
- 🇫🇷 French (fr)
- 🇸🇦 Arabic (ar)

Each lesson stores:
- Title (translated)
- Description (translated)
- Video URL (shared)
- Sort order (shared)
- Attachments (shared)
