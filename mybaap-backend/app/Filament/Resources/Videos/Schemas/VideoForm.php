<?php

namespace App\Filament\Resources\Videos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('thumbnail')
                    ->disk('public')
                    ->directory('thumbnails')
                    ->image()
                    ->imageEditor(),
                FileUpload::make('playback_path')
                    ->label('Video file')
                    ->disk('local')
                    ->directory('videos')
                    ->visibility('private')
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                    ->previewable(false)
                    ->openable(false)
                    ->downloadable(false)
                    ->required(fn ($livewire): bool => $livewire instanceof \App\Filament\Resources\Videos\Pages\CreateVideo),
                TextInput::make('duration_seconds')
                    ->numeric()
                    ->minValue(0),
                Toggle::make('is_paid')
                    ->default(true),
                Toggle::make('is_active')
                    ->default(true),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
