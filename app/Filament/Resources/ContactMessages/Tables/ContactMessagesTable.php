<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ContactMessage;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('contact_messages.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('contact_messages.fields.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->label(__('contact_messages.fields.subject'))
                    ->searchable()
                    ->limit(30),
                TextColumn::make('is_read')
                    ->badge()
                    ->color(fn(bool $state): string => match ($state) {
                        true => 'success',
                        false => 'warning',
                    })
                    ->formatStateUsing(fn(bool $state): string => $state ? __('contact_messages.fields.is_read') : __('contact_messages.fields.unread'))
                    ->label(__('contact_messages.fields.status')),
                TextColumn::make('created_at')
                    ->label(__('contact_messages.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('unread')
                    ->query(fn(Builder $query): Builder => $query->where('is_read', false))
                    ->label(__('contact_messages.filters.unread_only')),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markAsRead')
                    ->label(__('contact_messages.actions.mark_as_read'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn(ContactMessage $record): bool => $record->is_read)
                    ->action(function (ContactMessage $record) {
                        $record->update(['is_read' => true]);
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
