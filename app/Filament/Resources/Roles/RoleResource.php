<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\ManageRoles;
use App\Models\Permission;
use App\Models\Role;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function getNavigationGroup(): ?string
    {
        return __('laratrust.group_management');
    }

    protected static ?int $navigationSort = 2;

    public static function getLabel(): ?string
    {
        return __('laratrust.role');
    }

    public static function getPluralLabel(): ?string
    {
        return __('laratrust.roles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('laratrust.fields.name'))
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('display_name')
                    ->label(__('laratrust.fields.display_name'))
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label(__('laratrust.fields.description'))
                    ->rows(3)
                    ->maxLength(65535),

                CheckboxList::make('permissions')
                    ->label(__('laratrust.permissions'))
                    ->relationship(
                        name: 'permissions',
                        titleAttribute: 'display_name',
                        modifyQueryUsing: fn($query) => $query
                            ->withTranslation() // استخدم Scope الحزمة بدلاً من الـ Join اليدوي
                    )
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->display_name)
                    ->columns(3)
                    ->searchable()
                    ->bulkToggleable(),
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
                    ->sortable(false),

                TextColumn::make('permissions_count')
                    ->label(__('laratrust.permissions'))
                    ->counts('permissions')
                    ->badge()
                    ->color('success'),

                TextColumn::make('users_count')
                    ->label(__('laratrust.users'))
                    ->counts('users')
                    ->badge()
                    ->color('info'),

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
                    ->mutateRecordDataUsing(function (Role $record, array $data): array {
                        $locale = app()->getLocale();
                        // جلب الترجمة للغة الحالية
                        $translation = $record->translate($locale);

                        if ($translation) {
                            foreach ($record->translatedAttributes as $attribute) {
                                $data[$attribute] = $translation->{$attribute};
                            }
                        }

                        return $data;
                    })
                    ->using(function (Role $record, array $data): Role {
                        $locale = app()->getLocale();
                        $translatedAttributes = $record->translatedAttributes;

                        // 1. فصل البيانات الأساسية (مثل name)
                        $mainData = array_diff_key($data, array_flip($translatedAttributes));
                        $record->update($mainData);

                        // 2. تحديث بيانات الترجمة يدوياً لضمان الحفظ
                        $translationData = array_intersect_key($data, array_flip($translatedAttributes));

                        // هذه هي الطريقة الأضمن في Astrotomic
                        $record->translateOrNew($locale)->fill($translationData);

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
            'index' => ManageRoles::route('/'),
        ];
    }
}
