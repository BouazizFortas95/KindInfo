<?php

namespace App\Filament\Auth\Widgets;

use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    protected function getTableHeading(): string|null
    {
        return __('general.recent_activity');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return \App\Models\RecentActivity::query()
                    ->where('user_id', Auth::id())
                    ->orderBy('activity_date', 'desc');
            })
            ->columns([
                Tables\Columns\IconColumn::make('type')
                    ->label('')
                    ->icon(fn(string $state): string => match ($state) {
                        'lesson' => 'heroicon-m-play',
                        'badge' => 'heroicon-m-trophy',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'lesson' => 'primary',
                        'badge' => 'warning',
                        default => 'gray',
                    })
                    ->size('lg'),

                Tables\Columns\TextColumn::make('activity_description')
                    ->label(__('general.activity'))
                    ->state(function ($record) {
                        return $record->foreign_id;
                    })
                    ->formatStateUsing(function ($state, $record) {
                        // For auth user, "User" is always "You"
                        $userName = __('general.you');

                        if ($record->type === 'lesson') {
                            $lesson = Lesson::find($record->foreign_id);
                            $title = $lesson?->title ?? __('general.unknown_lesson');
                            return view('filament.components.activity-row', [
                                'user' => $userName,
                                'action' => __('general.completed'),
                                'subject' => $title,
                                'type' => 'lesson'
                            ])->render();
                        } else {
                            $badge = Badge::find($record->foreign_id);
                            $title = $badge?->name ?? __('general.unknown_badge');
                            return view('filament.components.activity-row', [
                                'user' => $userName,
                                'action' => __('general.earned'),
                                'subject' => $title,
                                'type' => 'badge'
                            ])->render();
                        }
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('activity_date')
                    ->label(__('general.date'))
                    ->date('M d, Y')
                    ->description(fn($record) => \Carbon\Carbon::parse($record->activity_date)->diffForHumans())
                    ->alignEnd(),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
