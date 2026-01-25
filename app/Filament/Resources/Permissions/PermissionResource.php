<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\Permissions\Pages\ManagePermissions;
use App\Models\Permission;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('laratrust.group_management');
    }

    public static function getLabel(): ?string
    {
        return __('laratrust.permission');
    }

    public static function getPluralLabel(): ?string
    {
        return __('laratrust.permissions');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('laratrust.fields.name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('display_name')
                    ->label(__('laratrust.fields.display_name'))
                    ->maxLength(255),

                Textarea::make('description')
                    ->label(__('laratrust.fields.description'))
                    ->rows(3)
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('laratrust.fields.name'))
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereLike('name', "%{$search}%");
                    })
                    ->sortable(),

                TextColumn::make('display_name')
                    ->label(__('laratrust.fields.display_name'))
                    ->getStateUsing(function ($record) {
                        return $record->getTranslation(app()->getLocale())?->display_name
                            ?? $record->name; // يعرض الاسم البرمجي كاحتياط
                    })
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereTranslationLike('display_name', "%{$search}%");
                    })
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('laratrust.fields.description'))
                    ->getStateUsing(function ($record) {
                        return $record->getTranslation(app()->getLocale())?->description
                            ?? $record->name; // يعرض الاسم البرمجي كاحتياط
                    })
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereTranslationLike('description', "%{$search}%");
                    })
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('roles_count')
                    ->label(__('laratrust.roles'))
                    ->counts('roles')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label(__('laratrust.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('4xl')
                    ->mutateRecordDataUsing(function (Permission $record, array $data): array {
                        $locale = app()->getLocale();
                        $translation = $record->translate($locale);

                        if ($translation) {
                            foreach ($record->translatedAttributes as $attribute) {
                                $data[$attribute] = $translation->{$attribute};
                            }
                        }
                        return $data;
                    })
                    ->using(function (Permission $record, array $data): Permission {
                        $locale = app()->getLocale();

                        // 1. تحديث الجدول الرئيسي
                        $record->update(['name' => $data['name']]);

                        // 2. تحديث جدول الترجمات يدوياً
                        // نستخدم fill مباشرة على موديل الترجمة لضمان الحفظ في SQLite
                        $translation = $record->translateOrNew($locale);
                        $translation->display_name = $data['display_name'];
                        $translation->description = $data['description'] ?? null;
                        $translation->save();

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
            'index' => ManagePermissions::route('/'),
        ];
    }
}
