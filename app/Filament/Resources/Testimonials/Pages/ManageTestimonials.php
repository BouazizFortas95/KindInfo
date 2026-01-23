<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\Testimonial;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\App;

class ManageTestimonials extends ManageRecords
{
    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('2xl')
                ->using(function (array $data): Testimonial {
                    $record = new (static::getModel())();

                    $locale = App::getLocale();

                    // Extract translation fields
                    $translationFields = ['content', 'client_title'];
                    $translationData = [];
                    foreach ($translationFields as $field) {
                        if (isset($data[$field])) {
                            $translationData[$field] = $data[$field];
                            unset($data[$field]);
                        }
                    }

                    // Fill main record data
                    $record->fill($data);

                    // Fill translation data for the current locale
                    $record->fill([
                        $locale => $translationData,
                    ]);

                    $record->save();

                    return $record;
                }),
        ];
    }
}
