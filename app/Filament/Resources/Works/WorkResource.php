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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Work Details')
                    ->schema([
                        TextInput::make('slug_main')
                            ->label('Main Slug')
                            ->default(fn() => (string) time())
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false),

                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Content')
                            ->columnSpanFull(),

                        FileUpload::make('feature_image')
                            ->label('Feature Image')
                            ->directory('works')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),

                        Select::make('categories')
                            ->label('Categories')
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('feature_image')
                    ->label('Image'),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereTranslationLike('title', "%{$search}%");
                    }),

                TextColumn::make('slug_main')
                    ->label('Main Slug')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label('Published'),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
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
