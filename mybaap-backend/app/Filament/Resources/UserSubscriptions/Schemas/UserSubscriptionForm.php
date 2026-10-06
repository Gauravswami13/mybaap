<?php

namespace App\Filament\Resources\UserSubscriptions\Schemas;

use App\Models\UserSubscription;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'mobile')
                    ->searchable()
                    ->required(),
                Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required(),
                DatePicker::make('start_date')
                    ->required()
                    ->default(now()->toDateString()),
                DatePicker::make('end_date')
                    ->required(),
                Select::make('status')
                    ->options([
                        UserSubscription::STATUS_ACTIVE => 'Active',
                        UserSubscription::STATUS_EXPIRED => 'Expired',
                        UserSubscription::STATUS_CANCELLED => 'Cancelled',
                    ])
                    ->default(UserSubscription::STATUS_ACTIVE)
                    ->required(),
                TextInput::make('payment_ref')
                    ->maxLength(120),
            ]);
    }
}
