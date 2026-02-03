---
description: Repository Information Overview
alwaysApply: true
---

# KindInfo Laravel Application Information

## Summary
**KindInfo** is a multilingual Laravel web application featuring course management, user authentication, and content localization. The project includes an admin panel built with Filament, role-based permissions using Laratrust, and translation support through Laravel Localization. It appears to be an educational platform with course content management capabilities.

## Structure
- **[app/](./app/)**: Core application logic including models, controllers, policies, and Livewire components
- **[resources/](./resources/)**: Frontend assets (CSS, JS), Blade templates, and Livewire views
- **[database/](./database/)**: Migrations, seeders, and factories for database schema
- **[tests/](./tests/)**: PHPUnit test suites (Feature and Unit tests)
- **[config/](./config/)**: Laravel configuration files for services, localization, and packages
- **[vendor/](./vendor/)**: Composer dependencies and Laravel IDE helpers

## Language & Runtime
**Language**: PHP  
**Version**: ^8.2  
**Framework**: Laravel ^12.0  
**Build System**: Composer + NPM/Vite  
**Package Manager**: Composer (PHP), NPM (JavaScript)

## Dependencies
**Main Dependencies**:
- **[laravel/framework](./composer.json:12)**: ^12.0 - Core Laravel framework
- **[filament/filament](./composer.json:11)**: ^5.0 - Admin panel framework
- **[santigarcor/laratrust](./composer.json:16)**: ^8.5 - Role and permission management
- **[mcamara/laravel-localization](./composer.json:15)**: ^2.3 - Multilingual support
- **[astrotomic/laravel-translatable](./composer.json:10)**: ^11.16 - Model translation
- **[livewire/volt](./composer.json:14)**: ^1.10 - Interactive components

**Development Dependencies**:
- **[phpunit/phpunit](./composer.json:25)**: ^11.5.3 - Testing framework
- **[laravel/pint](./composer.json:21)**: ^1.24 - PHP code style fixer
- **[laravel/sail](./composer.json:22)**: ^1.41 - Docker development environment
- **[tailwindcss](./package.json:14)**: ^4.0.0 - CSS framework
- **[vite](./package.json:15)**: ^7.0.7 - Frontend build tool

## Build & Installation
```bash
# Full setup (includes database migration and asset building)
composer run setup

# Development environment with concurrent services
composer run dev

# Install PHP dependencies only
composer install

# Install JavaScript dependencies and build assets
npm install && npm run build

# Development asset watching
npm run dev
```

## Testing
**Framework**: PHPUnit ^11.5.3  
**Test Location**: [tests/](./tests/) directory  
**Naming Convention**: `*Test.php` files in Feature and Unit subdirectories  
**Configuration**: PHPUnit configuration managed by Laravel

**Run Command**:
```bash
composer run test
# or directly
php artisan test
```

## Main Files & Entry Points
**Application Entry**: [artisan](./artisan) - Laravel CLI interface  
**Frontend Entry**: [resources/js/app.js](./resources/js/app.js) - Main JavaScript entry  
**Styling Entry**: [resources/css/app.css](./resources/css/app.css) - Main CSS file  
**Course Assets**: [resources/css/course-index.css](./resources/css/course-index.css), [resources/js/course-index.js](./resources/js/course-index.js)  
**Vite Config**: [vite.config.js](./vite.config.js) - Frontend build configuration  
**Environment**: [.env.example](./.env.example) - Environment configuration template

## Key Features & Components
- **Filament Admin Panel**: Course management and user administration
- **Multilingual Support**: English/Arabic localization with RTL support
- **Role-Based Access**: Laratrust integration for permissions
- **Course System**: Course creation, lessons, and user progress tracking
- **Livewire Components**: Interactive UI for course player and language switching
- **Translation Management**: Database-driven content translations