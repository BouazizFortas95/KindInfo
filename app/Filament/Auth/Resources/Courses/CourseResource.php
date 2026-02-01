<?php

namespace App\Filament\Auth\Resources\Courses;

use App\Filament\Auth\Resources\Courses\Pages\ListCourses;
use App\Filament\Auth\Resources\Courses\Pages\ViewCourse;
use App\Filament\Auth\Resources\Courses\Schemas\CourseForm;
use App\Filament\Auth\Resources\Courses\Schemas\CourseInfolist;
use App\Models\Course;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = null;

    public static function getModelLabel(): string
    {
        return __('courses.course');
    }

    public static function getPluralModelLabel(): string
    {
        return __('courses.courses');
    }

    public static function getNavigationLabel(): string
    {
        return __('courses.courses');
    }

    public static function form(Schema $schema): Schema
    {
        return CourseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actions([
                ViewAction::make(), // هذا الزر سيفتح الصفحة التي برمجناها للتو
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'view' => ViewCourse::route('/{record}'),
        ];
    }
}
