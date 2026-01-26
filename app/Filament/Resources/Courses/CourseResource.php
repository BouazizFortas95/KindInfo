<?php

namespace App\Filament\Resources\Courses;

use App\Filament\Resources\Courses\Pages\ManageCourses;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getLabel(): ?string
    {
        return __('courses.course');
    }

    public static function getPluralLabel(): ?string
    {
        return __('courses.courses');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Course')
                    ->tabs([
                        Tab::make(__('courses.course_details'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->label(__('courses.thumbnail'))
                                    ->directory('courses/thumbnails')
                                    ->image()
                                    ->imageEditor()
                                    ->columnSpanFull(),

                                Select::make('category_id')
                                    ->label(__('courses.categories'))
                                    ->relationship('category', 'id')
                                    ->getSearchResultsUsing(
                                        fn(string $search): array =>
                                        Category::whereTranslationLike('name', "%{$search}%")
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(fn($item) => [$item->id => $item->name])
                                            ->toArray()
                                    )
                                    ->getOptionLabelFromRecordUsing(fn(Category $record) => $record->name ?? '-')
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('price')
                                    ->label(__('courses.price'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->required(),

                                Toggle::make('is_active')
                                    ->label(__('courses.active'))
                                    ->default(true),
                            ]),

                        Tab::make(__('courses.translations'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('courses.title'))
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('description')
                                    ->label(__('courses.description'))
                                    ->rows(5)
                                    ->maxLength(65535),
                            ]),

                        Tab::make(__('courses.lessons'))
                            ->icon('heroicon-o-play-circle')
                            ->schema([
                                Repeater::make('lessons')
                                    ->label(__('courses.lessons'))
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn(array $state): ?string => $state['title'] ?? __('courses.new_lesson'))
                                    ->schema([
                                        Hidden::make('id'),

                                        TextInput::make('title')
                                            ->label(__('courses.lesson_title'))
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('video_url')
                                            ->label(__('courses.video_url'))
                                            ->url()
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('description')
                                            ->label(__('courses.lesson_description'))
                                            ->rows(3)
                                            ->maxLength(65535),

                                        FileUpload::make('attachments')
                                            ->label(__("courses.attachments"))
                                            ->disk('public')
                                            ->directory('courses/lessons/attachments')
                                            ->preserveFilenames()  // Keep original filenames
                                            ->visibility('public')  // Make files publicly accessible
                                            ->multiple()  // If you need multiple files
                                            ->downloadable()
                                            ->openable()
                                            ->reorderable()
                                            ->storeFileNamesIn('attachments')  // This stores file paths
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull()
                                    ->defaultItems(0)
                                    ->addActionLabel(__('courses.add_lesson')),

                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label(__('courses.thumbnail'))
                    ->circular(),

                TextColumn::make('category.name')
                    ->label(__('courses.category'))
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy(
                            \App\Models\CategoryTranslation::select('name')
                                ->whereColumn('category_translations.category_id', 'courses.category_id')
                                ->where('category_translations.locale', app()->getLocale())
                                ->limit(1),
                            $direction
                        );
                    })
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('category', function ($q) use ($search) {
                            $q->whereTranslationLike('name', "%{$search}%");
                        });
                    })
                    ->badge()
                    ->color('info'),

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

                TextColumn::make('lessons_count')
                    ->label(__('courses.lessons_count'))
                    ->counts('lessons')
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_active')
                    ->label(__('courses.active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('courses.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('courses.category'))
                    ->relationship('category', 'id')
                    // لجلب الأسماء المترجمة في قائمة الفلتر
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('is_active')->label(__('courses.active'))
                    ->options([
                        1 => __('courses.active'),
                        0 => __('courses.inactive'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('5xl')
                    ->mutateRecordDataUsing(function (Course $record, array $data): array {
                        $locale = App::getLocale();

                        // Load course translations for current locale
                        $translation = $record->translate($locale);
                        if ($translation) {
                            $data['title'] = $translation->title;
                            $data['description'] = $translation->description;
                        } else {
                            // Fallback to first available translation
                            $firstTranslation = $record->translations->first();
                            if ($firstTranslation) {
                                $data['title'] = $firstTranslation->title;
                                $data['description'] = $firstTranslation->description;
                            }
                        }

                        // Load lessons with their translations
                        $lessonsData = [];
                        foreach ($record->lessons()->orderBy('sort_order')->get() as $lesson) {
                            $lessonData = [
                                'id' => $lesson->id,
                                'video_url' => $lesson->video_url,
                                'attachments' => $lesson->attachments,
                                'title' => '',
                                'description' => '',
                            ];

                            // Get lesson translation for current locale
                            $lessonTranslation = $lesson->translate($locale);
                            if ($lessonTranslation) {
                                $lessonData['title'] = $lessonTranslation->title ?? '';
                                $lessonData['description'] = $lessonTranslation->description ?? '';
                            } else {
                                // Fallback to first available translation
                                $firstLessonTranslation = $lesson->translations->first();
                                if ($firstLessonTranslation) {
                                    $lessonData['title'] = $firstLessonTranslation->title ?? '';
                                    $lessonData['description'] = $firstLessonTranslation->description ?? '';
                                }
                            }

                            $lessonsData[] = $lessonData;
                        }

                        $data['lessons'] = $lessonsData;

                        return $data;
                    })
                    ->using(function (Course $record, array $data): Course {
                        return DB::transaction(function () use ($record, $data) {
                            $locale = App::getLocale();

                            // Update course main data (non-translatable)
                            $record->thumbnail = $data['thumbnail'] ?? $record->thumbnail;
                            $record->price = $data['price'] ?? $record->price;
                            $record->is_active = $data['is_active'] ?? $record->is_active;

                            // Update course translation for current locale using translateOrNew
                            $record->translateOrNew($locale)->title = $data['title'] ?? '';
                            $record->translateOrNew($locale)->description = $data['description'] ?? null;

                            $record->save();

                            // Handle lessons
                            $lessonsData = $data['lessons'] ?? [];
                            $existingLessonIds = $record->lessons()->pluck('id')->toArray();
                            $updatedLessonIds = [];

                            foreach ($lessonsData as $index => $lessonData) {
                                $lessonId = $lessonData['id'] ?? null;

                                if ($lessonId && in_array($lessonId, $existingLessonIds)) {
                                    // Update existing lesson
                                    $lesson = Lesson::find($lessonId);
                                    if ($lesson) {
                                        $lesson->video_url = $lessonData['video_url'] ?? '';
                                        $lesson->sort_order = $index;
                                        $lesson->attachments = $lessonData['attachments'] ?? [];

                                        // Update lesson translation for current locale
                                        $lesson->translateOrNew($locale)->title = $lessonData['title'] ?? '';
                                        $lesson->translateOrNew($locale)->description = $lessonData['description'] ?? null;

                                        $lesson->save();
                                        $updatedLessonIds[] = $lesson->id;
                                    }
                                } else {
                                    // Create new lesson
                                    $lesson = new Lesson();
                                    $lesson->course_id = $record->id;
                                    $lesson->video_url = $lessonData['video_url'] ?? '';
                                    $lesson->sort_order = $index;
                                    $lesson->attachments = $lessonData['attachments'] ?? [];

                                    // Set lesson translation for current locale
                                    $lesson->translateOrNew($locale)->title = $lessonData['title'] ?? '';
                                    $lesson->translateOrNew($locale)->description = $lessonData['description'] ?? null;

                                    $lesson->save();
                                    $updatedLessonIds[] = $lesson->id;
                                }
                            }

                            // Delete removed lessons
                            $lessonsToDelete = array_diff($existingLessonIds, $updatedLessonIds);
                            if (!empty($lessonsToDelete)) {
                                Lesson::whereIn('id', $lessonsToDelete)->delete();
                            }

                            return $record;
                        });
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
            'index' => ManageCourses::route('/'),
        ];
    }
}
