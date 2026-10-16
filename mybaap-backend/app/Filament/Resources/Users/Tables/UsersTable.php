<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mobile')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        User::STATUS_BLOCKED, User::STATUS_DELETED => 'danger',
                        default => 'success',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        User::STATUS_ACTIVE => 'Active',
                        User::STATUS_BLOCKED => 'Blocked',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('block')
                    ->label('Block')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => $record->status === User::STATUS_ACTIVE)
                    ->action(fn (User $record) => $record->update(['status' => User::STATUS_BLOCKED])),
                Action::make('unblock')
                    ->label('Unblock')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => $record->status === User::STATUS_BLOCKED)
                    ->action(fn (User $record) => $record->update(['status' => User::STATUS_ACTIVE])),
                EditAction::make(),
            ]);
    }
}
