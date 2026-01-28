<?php

namespace App\Filament\Auth\Resources\Courses\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Section::make(__('Course Content'))
                //     ->schema([
                //         RepeatableEntry::make('lessons')
                //             ->grid(2)
                //             ->schema([
                //                 Section::make()
                //                     ->compact() // لتقليل المسافات الافتراضية
                //                     ->schema([
                //                         ViewEntry::make('video_url')
                //                             ->label(false) // إخفاء العنوان ضروري للـ Full Width
                //                             ->view('filament.infolist.video-player')
                //                             ->columnSpanFull(),

                //                         Group::make([
                //                             TextEntry::make('title')
                //                                 ->label(__('courses.lesson_title'))
                //                                 ->weight('bold')
                //                                 ->size('lg'),

                //                             TextEntry::make('attachments')
                //                                 ->label(__('courses.attachments_download'))
                //                                 ->listWithLineBreaks() // بدون نقاط
                //                                 ->formatStateUsing(fn($state) => basename($state))
                //                                 ->icon('heroicon-m-paper-clip')
                //                                 ->url(fn($state) => Storage::disk('public')->url($state))
                //                                 ->openUrlInNewTab()
                //                                 ->badge()
                //                                 ->color('info'),
                //                         ])->extraAttributes(['class' => 'px-4 pb-4']) // إضافة الهوامش للنصوص فقط وليس للفيديو
                //                     ])
                //             ])
                //     ])
            ])->columns(1);
    }
}
