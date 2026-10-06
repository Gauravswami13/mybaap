<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('mobile')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('name')
                    ->maxLength(80),
                Select::make('status')
                    ->options([
                        User::STATUS_ACTIVE => 'Active',
                        User::STATUS_BLOCKED => 'Blocked',
                    ])
                    ->required(),
            ]);
    }
}
