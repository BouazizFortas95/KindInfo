<?php

namespace App\Filament\Resources\Works;

use App\Filament\Resources\Works\Pages\ManageWorks;
use App\Models\Work;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class WorkResource extends Resource
{
    protected static ?string $model = Work::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'slug_main';

    public static function getLabel(): ?string
    {
        return __('works.work');
    }

    public static function getPluralLabel(): ?string
    {
        return __('works.works');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug_main')
                    ->label(__('works.main_slug'))
                    ->default(fn() => (string) time())
                    ->disabled()
                    ->dehydrated(true)
                    ->required()
                    ->maxLength(255),

                Toggle::make('is_published')
                    ->label(__('works.published'))
                    ->default(false),

                TextInput::make('title')
                    ->label(__('works.title'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label(__('works.slug'))
                    ->disabled()
                    ->dehydrated(true)
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label(__('works.description'))
                    ->rows(3)
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label(__('works.content'))
                    ->columnSpanFull(),

                FileUpload::make('feature_image')
                    ->label(__('works.feature_image'))
                    ->directory('works')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),

                Select::make('categories')
                    ->label(__('works.categories'))
                    ->relationship('categories')
                    ->getSearchResultsUsing(
                        fn(string $search): array =>
                        \App\Models\Category::whereTranslationLike('name', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->getOptionLabelFromRecordUsing(fn(\App\Models\Category $record) => $record->name ?? $record->translations->first()?->name ?? 'No Name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
            ])
            ->extraAttributes(['class' => 'custom-section-style']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('feature_image')
                    ->label(__('works.image')),

                TextColumn::make('title')
                    ->label(__('works.title'))
                    ->state(fn(Work $record): ?string => $record->translate(app()->getLocale())?->title)
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereTranslationLike('title', "%{$search}%");
                    }),

                TextColumn::make('slug_main')
                    ->label(__('works.main_slug'))
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label(__('works.published')),

                TextColumn::make('created_at')
                    ->label(__('works.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('7xl')
                    ->using(function (Work $record, array $data): Work {
                        $record->fill($data);
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
            'index' => ManageWorks::route('/'),
        ];
    }
}
