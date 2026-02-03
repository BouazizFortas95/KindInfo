<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BadgeResource\Pages;
use App\Models\Badge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BadgeResource extends Resource
{
    protected static ?string $model = Badge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('general.achievements');
    }

    public static function getNavigationLabel(): string
    {
        return __('badges.resource.plural_label');
    }

    public static function getModelLabel(): string
    {
        return __('badges.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('badges.resource.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('icon_path')
                    ->label(__('badges.fields.icon_path'))
                    ->image()
                    ->directory('badges')
                    ->columnSpanFull(),

                Select::make('type')
                    ->options([
                        'course_completion' => __('badges.types.course_completion'),
                        'points_achievement' => __('badges.types.points_achievement'),
                        'special_achievement' => __('badges.types.special_achievement'),
                    ])
                    ->required(),

                TextInput::make('points_required')
                    ->label(__('badges.fields.points_required'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label(__('badges.fields.is_active'))
                    ->default(true),

                Tabs::make(__('badges.tabs.translations'))
                    ->tabs(
                        collect(LaravelLocalization::getSupportedLocales())
                            ->map(function ($locale, $code) {
                                return Tab::make($locale['name'])
                                    ->schema([
                                        TextInput::make("{$code}.name")
                                            ->label(__('badges.fields.name'))
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make("{$code}.description")
                                            ->label(__('badges.fields.description'))
                                            ->rows(3),
                                    ]);
                            })
                            ->toArray()
                    )
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon_path')
                    ->label(__('badges.fields.icon_path'))
                    ->square()
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('badges.fields.name'))
                    ->getStateUsing(fn(Badge $record) => $record->name)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'course_completion' => 'success',
                        'points_achievement' => 'warning',
                        'special_achievement' => 'info',
                    }),

                TextColumn::make('points_required')
                    ->label(__('badges.fields.points_required'))
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label(__('badges.fields.is_active')),

                TextColumn::make('created_at')
                    ->label(__('badges.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('badges.filters.type'))
                    ->options([
                        'course_completion' => __('badges.types.course_completion'),
                        'points_achievement' => __('badges.types.points_achievement'),
                        'special_achievement' => __('badges.types.special_achievement'),
                    ]),
                TernaryFilter::make('is_active')
                    ->label(__('badges.filters.is_active')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBadges::route('/'),
            'create' => Pages\CreateBadge::route('/create'),
            'edit' => Pages\EditBadge::route('/{record}/edit'),
        ];
    }
}