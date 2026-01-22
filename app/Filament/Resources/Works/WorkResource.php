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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
                Section::make('General Information')
                    ->schema([
                        TextInput::make('slug_main')
                            ->label('Slug (Main)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false),

                        FileUpload::make('feature_image')
                            ->label('Feature Image')
                            ->directory('works')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),

                        Select::make('categories')
                            ->label('Categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Translations')
                    ->schema([
                        Tabs::make('translations')
                            ->tabs([
                                Tab::make('English')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        TextInput::make('translations.en.title')
                                            ->label('Title (EN)')
                                            ->required(fn(string $operation) => $operation === 'create')
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('translations.en.slug', Str::slug($state))),

                                        TextInput::make('translations.en.slug')
                                            ->label('Slug (EN)')
                                            ->required(fn(string $operation) => $operation === 'create')
                                            ->maxLength(255),

                                        Textarea::make('translations.en.description')
                                            ->label('Description (EN)')
                                            ->rows(3),

                                        RichEditor::make('translations.en.content')
                                            ->label('Content (EN)')
                                            ->columnSpanFull(),
                                    ]),

                                Tab::make('العربية')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        TextInput::make('translations.ar.title')
                                            ->label('العنوان (AR)')
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('translations.ar.slug', Str::slug($state))),

                                        TextInput::make('translations.ar.slug')
                                            ->label('الرابط (AR)')
                                            ->maxLength(255),

                                        Textarea::make('translations.ar.description')
                                            ->label('الوصف (AR)')
                                            ->rows(3),

                                        RichEditor::make('translations.ar.content')
                                            ->label('المحتوى (AR)')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
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
                    ->state(fn(Work $record): ?string => $record->translate(app()->getLocale())?->title)
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
                EditAction::make()
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
