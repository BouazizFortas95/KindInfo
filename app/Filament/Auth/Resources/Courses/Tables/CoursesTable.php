<?php

namespace App\Filament\Auth\Resources\Courses\Tables;

use App\Models\CategoryTranslation;
use App\Models\Course;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([ // هذا السطر يحول الجدول إلى شبكة
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    ImageColumn::make('thumbnail')
                        ->label(__('courses.thumbnail'))
                        ->height(160)
                        ->width('100%'),

                    // TextColumn::make('category.name')
                    //     ->label(__('courses.category'))
                    //     ->sortable(query: function ($query, string $direction) {
                    //         return $query->orderBy(
                    //             CategoryTranslation::select('name')
                    //                 ->whereColumn('category_translations.category_id', 'courses.category_id')
                    //                 ->where('category_translations.locale', app()->getLocale())
                    //                 ->limit(1),
                    //             $direction
                    //         );
                    //     })
                    //     ->searchable(query: function ($query, string $search) {
                    //         return $query->whereHas('category', function ($q) use ($search) {
                    //             $q->whereTranslationLike('name', "%{$search}%");
                    //         });
                    //     })
                    //     ->badge()
                        // ->color('info'),

                    TextColumn::make('title')
                        ->label(__('courses.title'))
                        ->state(fn(Course $record): ?string => $record->translate(App::getLocale())?->title ?? $record->translations->first()?->title)
                        ->searchable(query: function (Builder $query, string $search): Builder {
                            return $query->whereTranslationLike('title', "%{$search}%");
                        }),

                    TextColumn::make('price')
                        ->label(__('courses.price'))
                        ->money('USD')
                        ->sortable(),

                    // TextColumn::make('lessons_count')
                    //     ->label(__('courses.lessons_count'))
                    //     ->counts('lessons')
                    //     ->badge()
                    //     ->color('success'),

                    // IconColumn::make('is_active')
                    //     ->label(__('courses.active'))
                    //     ->boolean(),

                    // TextColumn::make('created_at')
                    //     ->label(__('courses.created_at'))
                    //     ->dateTime()
                    //     ->sortable()
                    //     ->toggleable(isToggledHiddenByDefault: true),
//
                ]),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
