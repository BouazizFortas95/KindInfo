<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('categories.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('categories.resource.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('categories.resource.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('parent_id')
                    ->label(__('categories.fields.parent'))
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn(string $search): array =>
                        Category::whereTranslationLike('name', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->getOptionLabelUsing(fn($value): ?string => Category::find($value)?->name)
                    ->placeholder(__('categories.fields.select_parent')),

                Section::make()
                    ->schema([
                        \Filament\Forms\Components\Select::make('form_locale')
                            ->label(__('categories.fields.edit_language'))
                            ->options([
                                'en' => 'English',
                                'ar' => 'العربية',
                                'fr' => 'Français',
                            ])
                            ->default(app()->getLocale())
                            ->live()
                            ->selectablePlaceholder(false)
                            ->native(false)
                            ->prefixIcon('heroicon-m-language')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                // English Group
                                Group::make([
                                    TextInput::make('en.name')
                                        ->label(__('categories.fields.name') . ' (EN)')
                                        ->required(fn(string $operation) => $operation === 'create')
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('en.slug', \Illuminate\Support\Str::slug($state)))
                                        ->dehydrated(true),
                                    TextInput::make('en.slug')
                                        ->label(__('categories.fields.slug') . ' (EN)')
                                        ->maxLength(255)
                                        ->disabled()
                                        ->dehydrated(true),
                                ])
                                    ->extraAttributes(fn($get) => [
                                        'style' => $get('form_locale') !== 'en' ? 'display: none' : ''
                                    ])
                                    ->columnSpanFull(),

                                // Arabic Group
                                Group::make([
                                    TextInput::make('ar.name')
                                        ->label(__('categories.fields.name') . ' (AR)')
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('ar.slug', \Illuminate\Support\Str::slug($state)))
                                        ->dehydrated(true),
                                    TextInput::make('ar.slug')
                                        ->label(__('categories.fields.slug') . ' (AR)')
                                        ->maxLength(255)
                                        ->disabled()
                                        ->dehydrated(true),
                                ])
                                    ->extraAttributes(fn($get) => [
                                        'style' => $get('form_locale') !== 'ar' ? 'display: none' : ''
                                    ])
                                    ->columnSpanFull(),

                                // French Group
                                Group::make([
                                    TextInput::make('fr.name')
                                        ->label(__('categories.fields.name') . ' (FR)')
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('fr.slug', \Illuminate\Support\Str::slug($state)))
                                        ->dehydrated(true),
                                    TextInput::make('fr.slug')
                                        ->label(__('categories.fields.slug') . ' (FR)')
                                        ->maxLength(255)
                                        ->disabled()
                                        ->dehydrated(true),
                                ])
                                    ->extraAttributes(fn($get) => [
                                        'style' => $get('form_locale') !== 'fr' ? 'display: none' : ''
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('categories.fields.name'))
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereTranslationLike('name', "%{$search}%");
                    }),
                TextColumn::make('slug')
                    ->label(__('categories.fields.slug'))
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereTranslationLike('slug', "%{$search}%");
                    }),
                TextColumn::make('parent.name')
                    ->label(__('categories.fields.parent'))
                    ->placeholder('-')
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereHas('parent', function ($query) use ($search) {
                            $query->whereTranslationLike('name', "%{$search}%");
                        });
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('categories.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->using(function (Category $record, array $data): Category {
                        $record->fill($data);
                        $record->parent_id = $data['parent_id'] ?? null;
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
            'index' => ManageCategories::route('/'),
        ];
    }
}
