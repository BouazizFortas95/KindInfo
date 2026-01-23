<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('client_company')
                    ->maxLength(255),
                FileUpload::make('client_avatar')
                    ->image()
                    ->avatar()
                    ->directory('testimonials/avatars'),
                Select::make('rating')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ])
                    ->default(5)
                    ->required(),
                Toggle::make('is_visible')
                    ->default(true),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),

                // Translated fields
                TextInput::make('client_title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('content')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }
}
