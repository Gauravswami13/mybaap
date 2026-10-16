<?php

namespace App\Filament\Resources\UserSubscriptions\Tables;

use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('plan.price')
                    ->label('Price'),
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
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        UserSubscription::STATUS_ACTIVE => 'Active',
                        UserSubscription::STATUS_EXPIRED => 'Expired',
                        UserSubscription::STATUS_CANCELLED => 'Cancelled',
                    ]),
                SelectFilter::make('window')
                    ->label('Period')
                    ->options([
                        'active' => 'Active now',
                        'this_month' => 'This month',
                        'last_month' => 'Last month',
                        'expiring' => 'Expiring in 7 days',
                        'expired' => 'Expired',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $today = today()->toDateString();

                        return match ($data['value'] ?? null) {
                            'active' => $query
                                ->where('status', UserSubscription::STATUS_ACTIVE)
                                ->whereDate('end_date', '>=', $today),
                            'this_month' => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                            'last_month' => $query->whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()]),
                            'expiring' => $query
                                ->where('status', UserSubscription::STATUS_ACTIVE)
                                ->whereDate('end_date', '>=', $today)
                                ->whereDate('end_date', '<=', today()->addDays(7)->toDateString()),
                            'expired' => $query
                                ->whereDate('end_date', '<', $today)
                                ->where('status', '!=', UserSubscription::STATUS_CANCELLED),
                            default => $query,
                        };
                    }),
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
