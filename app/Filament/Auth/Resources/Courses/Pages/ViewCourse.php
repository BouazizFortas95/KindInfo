<?php

namespace App\Filament\Auth\Resources\Courses\Pages;

use App\Filament\Auth\Resources\Courses\CourseResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;


    protected string $view = 'filament.auth.resources.course-resource.pages.view-course';   

}
