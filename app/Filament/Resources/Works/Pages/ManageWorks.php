<?php

namespace App\Filament\Resources\Works\Pages;

use App\Filament\Resources\Works\WorkResource;
use App\Models\Work;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWorks extends ManageRecords
{
    protected static string $resource = WorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data, string $model): Work {
                    $record = new Work();
                    $record->fill($data);
                    $record->save();
                    return $record;
                }),
        ];
    }
}
