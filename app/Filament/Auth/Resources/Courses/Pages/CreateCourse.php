<?php

namespace App\Filament\Auth\Resources\Courses\Pages;

use App\Filament\Auth\Resources\Courses\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;
}
