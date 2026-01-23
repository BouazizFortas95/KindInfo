<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('client_avatar')
                    ->label(__('testimonials.fields.client_avatar'))
                    ->circular(),
                TextColumn::make('client_name')
                    ->label(__('testimonials.fields.client_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client_company')
                    ->label(__('testimonials.fields.client_company'))
                    ->searchable(),
                TextColumn::make('rating')
                    ->label(__('testimonials.fields.rating'))
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label(__('testimonials.fields.is_visible'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('order')
                    ->label(__('testimonials.fields.order'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('testimonials.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('2xl')
                    ->mutateRecordDataUsing(function (Model $record, array $data): array {
                        $locale = App::getLocale();
                        $translation = $record->translate($locale);

                        if ($translation) {
                            $data['content'] = $translation->content;
                            $data['client_title'] = $translation->client_title;
                        }

                        return $data;
                    })
                    ->using(function (Model $record, array $data): Model {
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
