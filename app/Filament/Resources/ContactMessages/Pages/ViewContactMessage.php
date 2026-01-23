<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Reply via Email')
                ->icon('heroicon-o-arrow-turn-up-left')
                ->color('primary')
                ->url(fn($record): string => "mailto:{$record->email}?subject=Re: {$record->subject}"),
        ];
    }

    protected function afterFill(): void
    {
        if (!$this->getRecord()->is_read) {
            $this->getRecord()->update(['is_read' => true]);
        }
    }
}
