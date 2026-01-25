<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\ManageServices;
use App\Models\Service;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getLabel(): ?string
    {
        return __('services.service');
    }

    public static function getPluralLabel(): ?string
    {
        return __('services.services');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('services.title'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label(__('services.slug'))
                    ->disabled()
                    ->dehydrated(true)
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label(__('services.description'))
                    ->rows(3)
                    ->maxLength(65535),

                TextInput::make('icon')
                    ->label(__('services.icon')),

                Toggle::make('is_active')
                    ->label(__('services.active'))
                    ->default(true),

                TextInput::make('order')
                    ->label(__('services.order'))
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('services.title'))
                    ->state(fn(Service $record): ?string => $record->translate(App::getLocale())?->title ?? $record->translations->first()?->title)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereTranslationLike('title', "%{$search}%");
                    }),

                TextColumn::make('slug')
                    ->label(__('services.slug'))
                    ->state(fn(Service $record): ?string => $record->translate(App::getLocale())?->slug ?? $record->translations->first()?->slug)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereTranslationLike('slug', "%{$search}%");
                    }),

                TextColumn::make('icon')
                    ->label(__('services.icon')),

                IconColumn::make('is_active')
                    ->label(__('services.active'))
                    ->boolean(),

                TextColumn::make('order')
                    ->label(__('services.order'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('services.created_at'))
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
                    ->mutateRecordDataUsing(function (Service $record, array $data): array {
                        $locale = App::getLocale();
                        $translation = $record->translate($locale);

                        if ($translation) {
                            foreach ($record->getTranslatableAttributes() as $attribute) {
                                $data[$attribute] = $translation->{$attribute};
                            }
                        }

                        return $data;
                    })
                    ->using(function (Service $record, array $data): Service {
                        $translatableAttributes = $record->getTranslatableAttributes();

                        $mainData = array_diff_key($data, array_flip($translatableAttributes));
                        $translationData = array_intersect_key($data, array_flip($translatableAttributes));

                        $record->fill($mainData);

                        // Update translation for the current app locale
                        $locale = App::getLocale();
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

    public static function getPages(): array
    {
        return [
            'index' => ManageServices::route('/'),
        ];
    }
}
