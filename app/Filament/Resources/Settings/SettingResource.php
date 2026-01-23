<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\ManageSettings;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $recordTitleAttribute = null;

    public static function getModelLabel(): string
    {
        return __('settings.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('settings.resource.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.resource.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('logo')
                    ->label(__('settings.form.logo.label'))
                    ->image()
                    ->imageEditor()
                    ->directory('site')
                    ->visibility('public')
                    ->columnSpan(1)
                    ->extraAttributes(['class' => 'dropzone-style'])
                    ->placeholder(__('settings.form.logo.placeholder'))
                    ->imagePreviewHeight('250')
                    ->loadingIndicatorPosition('left')
                    ->removeUploadedFileButtonPosition('right')
                    ->uploadButtonPosition('left')
                    ->uploadProgressIndicatorPosition('left'),
                FileUpload::make('favicon')
                    ->label(__('settings.form.favicon.label'))
                    ->image()
                    ->imageEditor()
                    ->directory('site')
                    ->placeholder(__('settings.form.favicon.placeholder'))
                    ->columnSpan(1),
                TextInput::make('email')
                    ->label(__('settings.form.email.label'))
                    ->email(),
                TextInput::make('phone')
                    ->label(__('settings.form.phone.label'))
                    ->tel(),
                TextInput::make('facebook_url')
                    ->label(__('settings.form.facebook_url.label'))
                    ->url(),
                TextInput::make('twitter_url')
                    ->label(__('settings.form.twitter_url.label'))
                    ->url(),
                TextInput::make('linkedin_url')
                    ->label(__('settings.form.linkedin_url.label'))
                    ->url(),
                TextInput::make('github_url')
                    ->label(__('settings.form.github_url.label'))
                    ->url(),
            ])
            ->columns(2);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tab::make('Identity')
                    ->icon('heroicon-m-finger-print')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Website Logo')
                            ->image()
                            ->imageEditor() 
                            ->directory('site')
                            ->visibility('public')
                            ->columnSpanFull() 
                            ->extraAttributes(['class' => 'dropzone-style']) 
                            ->placeholder('Drag and drop your logo here or click to browse')
                            ->imagePreviewHeight('250')
                            ->loadingIndicatorPosition('left')
                            ->removeUploadedFileButtonPosition('right')
                            ->uploadButtonPosition('left')
                            ->uploadProgressIndicatorPosition('left'),

                        FileUpload::make('favicon')
                            ->label('Favicon')
                            ->image()
                            ->avatar() 
                            ->imageEditor()
                            ->directory('site')
                            ->placeholder('Upload Favicon')
                            ->columnSpan(1),
                    ])->columns(2),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('facebook_url')
                    ->placeholder('-'),
                TextEntry::make('twitter_url')
                    ->placeholder('-'),
                TextEntry::make('linkedin_url')
                    ->placeholder('-'),
                TextEntry::make('github_url')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Settings')
            ->columns([
                ImageColumn::make('logo')
                    ->circular()
                    ->label(__('settings.table.logo')),
                ImageColumn::make('favicon')
                    ->circular()
                    ->label(__('settings.table.favicon')),
                TextColumn::make('email')
                    ->label(__('settings.table.email'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('settings.table.phone'))
                    ->searchable(),
                TextColumn::make('facebook_url')
                    ->label(__('settings.table.facebook_url'))
                    ->searchable(),
                TextColumn::make('twitter_url')
                    ->label(__('settings.table.twitter_url'))
                    ->searchable(),
                TextColumn::make('linkedin_url')
                    ->label(__('settings.table.linkedin_url'))
                    ->searchable(),
                TextColumn::make('github_url')
                    ->label(__('settings.table.github_url'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('settings.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('settings.table.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ManageSettings::route('/'),
        ];
    }
}
