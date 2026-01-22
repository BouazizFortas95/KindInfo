<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Models\Category;
use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data, string $model): Category {
                    $record = new Category();
                    $record->fill($data);
                    $record->parent_id = $data['parent_id'] ?? null;
                    $record->save();
                    return $record;
                }),
        ];
    }
}