<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\App;

class ManageServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('2xl')
                ->using(function (array $data): Service {
                    $model = static::getModel();
                    $record = new $model();

                    $translatableAttributes = $record->getTranslatableAttributes();

                    $mainData = array_diff_key($data, array_flip($translatableAttributes));
                    $translationData = array_intersect_key($data, array_flip($translatableAttributes));

                    $record->fill($mainData);

                    // Add translation for the current app locale
                    $locale = App::getLocale();
                    $record->fill([
                        $locale => $translationData,
                    ]);

                    $record->save();

                    return $record;
                }),
        ];
    }
}
