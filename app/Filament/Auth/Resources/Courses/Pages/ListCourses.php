<?php

namespace App\Filament\Auth\Resources\Courses\Pages;

use App\Filament\Auth\Resources\Courses\CourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected string $view = 'filament.auth.resources.course-resource.pages.list-courses';
   
}
