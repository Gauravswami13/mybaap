<?php

namespace App\Filament\Resources\UserSubscriptions\Tables;

use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.mobile')
                    ->label('Mobile')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('plan.name')
                    ->label('Plan'),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('payment_ref')
                    ->toggleable(),
            ])
            ->recordActions([
                Action::make('activate')
                    ->label('Activate')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (UserSubscription $record): bool => $record->status !== UserSubscription::STATUS_ACTIVE)
                    ->action(fn (UserSubscription $record) => app(SubscriptionService::class)->manualActivate($record)),
                Action::make('extend')
                    ->label('Extend')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (UserSubscription $record) => app(SubscriptionService::class)->extend($record)),
                Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (UserSubscription $record): bool => $record->status !== UserSubscription::STATUS_CANCELLED)
                    ->action(fn (UserSubscription $record) => app(SubscriptionService::class)->cancel($record)),
                EditAction::make(),
            ]);
    }
}
