<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $recordTitleAttribute = null;

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
                Select::make('parent_id')
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
                        TextInput::make('name')
                            ->label(__('categories.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        TextInput::make('slug')
                            ->label(__('categories.fields.slug'))
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
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
            'index' => ManageCategories::route('/'),
        ];
    }
}
