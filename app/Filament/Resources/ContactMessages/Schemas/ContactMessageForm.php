<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('contact_messages.fields.name'))
                    ->disabled(),
                TextInput::make('email')
                    ->label(__('contact_messages.fields.email'))
                    ->disabled(),
                TextInput::make('subject')
                    ->label(__('contact_messages.fields.subject'))
                    ->disabled(),
                Textarea::make('message')
                    ->label(__('contact_messages.fields.message'))
                    ->disabled()
                    ->rows(10),
                Toggle::make('is_read')
                    ->label(__('contact_messages.fields.has_been_read'))
                    ->disabled(),
            ]);
    }
}
