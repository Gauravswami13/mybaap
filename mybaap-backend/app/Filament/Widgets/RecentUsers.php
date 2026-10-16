<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentUsers extends TableWidget
{
    protected static ?int $sort = 3;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent users')
            ->query(
                User::query()->with(['latestSubscription.plan'])->latest()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('mobile')
                    ->searchable(),
                TextColumn::make('latestSubscription.plan.name')
                    ->label('Plan')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        User::STATUS_BLOCKED, User::STATUS_DELETED => 'danger',
                        default => 'success',
                    }),
                TextColumn::make('latestSubscription.status')
                    ->label('Subscription')
                    ->badge()
                    ->placeholder('None'),
                TextColumn::make('latestSubscription.end_date')
                    ->label('Expiry')
                    ->date()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date(),
            ])
            ->headerActions([
                Action::make('viewAll')
                    ->label('View all users')
                    ->url(UserResource::getUrl()),
            ])
            ->paginated([8])
            ->defaultPaginationPageOption(8);
    }
}
