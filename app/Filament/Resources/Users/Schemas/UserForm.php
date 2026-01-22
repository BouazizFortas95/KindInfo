<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('users.fields.name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('users.fields.email'))
                    ->email()
                    ->required()
                    ->autocomplete('off'),
                DateTimePicker::make('email_verified_at')
                    ->label(__('users.fields.email_verified_at')),
                TextInput::make('password')
                    ->label(__('users.fields.password'))
                    ->password()
                    ->autocomplete('new-password')
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
            ]);
    }
}
