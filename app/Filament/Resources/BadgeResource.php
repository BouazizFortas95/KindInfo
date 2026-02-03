<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BadgeResource\Pages;
use App\Models\Badge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\FileUpload;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Toggle;
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('icon_path')
                    ->label('Badge Icon')
                    ->image()
                    ->directory('badges')
                    ->columnSpanFull(),

                Select::make('type')
                    ->options([
                        'course_completion' => 'Course Completion',
                        'points_achievement' => 'Points Achievement',
                        'special_achievement' => 'Special Achievement',
                    ])
                    ->required(),

                TextInput::make('points_required')
                    ->label('Points Required')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Tabs::make('Translations')
                    ->tabs(
                        collect(LaravelLocalization::getSupportedLocales())
                            ->map(function ($locale, $code) {
                                return Tab::make($locale['name'])
                                    ->schema([
                                        TextInput::make("{$code}.name")
                                            ->label('Name')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make("{$code}.description")
                                            ->label('Description')
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
                    ->label('Icon')
                    ->square()
                    ->size(40),

                TextColumn::make('name')
                    ->label('Name')
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
                    ->label('Points Required')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'course_completion' => 'Course Completion',
                        'points_achievement' => 'Points Achievement',
                        'special_achievement' => 'Special Achievement',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active'),
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