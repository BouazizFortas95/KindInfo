<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Arr;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSettings extends ManageRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('5xl')
                ->using(function (array $data): \Illuminate\Database\Eloquent\Model {
                    $transFields = ['site_name', 'meta_title', 'meta_description'];
                    $record = \App\Models\Setting::create(Arr::except($data, $transFields));
                    $record->translateOrNew(app()->getLocale())->fill(Arr::only($data, $transFields));
                    $record->save();
                    return $record;
                })->visible(fn() => \App\Models\Setting::count() < 1),
        ];
    }
}
