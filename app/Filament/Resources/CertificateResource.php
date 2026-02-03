<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\DateTimePicker;
use Filament\Schemas\Components\RichEditor;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('general.achievements');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'id')
                    ->getSearchResultsUsing(
                        fn(string $search): array =>
                        Course::whereTranslationLike('title', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn($item) => [$item->id => $item->title])
                            ->toArray()
                    )
                    ->getOptionLabelFromRecordUsing(fn(Course $record) => $record->title ?? '-')
                    ->preload()
                    ->searchable()
                    ->required(),

                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('certificate_uuid')
                    ->label('Certificate UUID')
                    ->disabled()
                    ->dehydrated(false),

                DateTimePicker::make('issued_at')
                    ->label('Issued At')
                    ->required()
                    ->default(now()),

                Tabs::make('Translations')
                    ->tabs(
                        collect(LaravelLocalization::getSupportedLocales())
                            ->map(function ($locale, $code) {
                                return Tab::make($locale['name'])
                                    ->schema([
                                        TextInput::make("{$code}.title")
                                            ->label('Title')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make("{$code}.reason")
                                            ->label('Reason')
                                            ->rows(2),

                                        RichEditor::make("{$code}.body")
                                            ->label('Certificate Body')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'bulletList',
                                                'orderedList',
                                            ]),
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
                TextColumn::make('title')
                    ->label('Title')
                    ->getStateUsing(fn(Certificate $record) => $record->title)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->getStateUsing(fn(Certificate $record) => $record->course?->title)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('certificate_uuid')
                    ->label('UUID')
                    ->limit(15)
                    ->tooltip(fn(Certificate $record) => $record->certificate_uuid)
                    ->copyable(),

                TextColumn::make('issued_at')
                    ->label('Issued At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'id')
                    ->getSearchResultsUsing(
                        fn(string $search): array =>
                        Course::whereTranslationLike('title', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn($item) => [$item->id => $item->title])
                            ->toArray()
                    )
                    ->getOptionLabelFromRecordUsing(fn(Course $record) => $record->title ?? '-'),
            ])
            ->defaultSort('issued_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'view' => Pages\ViewCertificate::route('/{record}'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }
}