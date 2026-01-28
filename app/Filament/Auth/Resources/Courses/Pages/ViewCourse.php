<?php

namespace App\Filament\Auth\Resources\Courses\Pages;

use App\Filament\Auth\Resources\Courses\CourseResource;
use Astrotomic\Translatable\Translatable;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Schemas\Schema;

class ViewCourse extends ViewRecord
{
    // use Translatable;

    protected static string $resource = CourseResource::class;

    protected string $view = 'filament.auth.resources.course-resource.pages.view-course';

}
