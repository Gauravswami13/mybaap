<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Video;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', (string) User::query()->count()),
            Stat::make('Deleted accounts', (string) User::onlyTrashed()->count()),
            Stat::make('Active subscriptions', (string) UserSubscription::query()
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->whereDate('end_date', '>=', today())
                ->count()),
            Stat::make('Videos', (string) Video::query()->count()),
            Stat::make('Free videos', (string) Video::query()->where('is_paid', false)->count()),
            Stat::make('Paid videos', (string) Video::query()->where('is_paid', true)->count()),
        ];
    }
}
