<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SubscriptionPlans\SubscriptionPlanResource;
use App\Filament\Resources\UserSubscriptions\UserSubscriptionResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Videos\VideoResource;
use App\Models\User;
use App\Services\DashboardService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Overview';

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $kpis = app(DashboardService::class)->kpis();

        return [
            Stat::make('Total users', (string) $kpis['users'])
                ->description('App accounts')
                ->descriptionColor('success')
                ->chart($this->spark((int) $kpis['users']))
                ->chartColor('success')
                ->icon(Heroicon::OutlinedUsers)
                ->color('gray')
                ->url(UserResource::getUrl()),
            Stat::make('Active subscriptions', (string) $kpis['active_subscriptions'])
                ->description('Active and not expired')
                ->descriptionColor('success')
                ->chart($this->spark((int) $kpis['active_subscriptions']))
                ->chartColor('success')
                ->icon(Heroicon::OutlinedCreditCard)
                ->color('gray')
                ->url(UserSubscriptionResource::getUrl('index', [
                    'tableFilters' => ['window' => ['value' => 'active']],
                ])),
            Stat::make('Active plans', (string) $kpis['plans'])
                ->description('Plans users can buy')
                ->chart($this->spark((int) $kpis['plans']))
                ->chartColor('success')
                ->icon(Heroicon::OutlinedTag)
                ->color('gray')
                ->url(SubscriptionPlanResource::getUrl()),
            Stat::make('Expiring soon', (string) $kpis['expiring'])
                ->description('Ends within 7 days')
                ->chart($this->spark((int) $kpis['expiring']))
                ->chartColor('warning')
                ->icon(Heroicon::OutlinedCalendar)
                ->color('gray')
                ->url(UserSubscriptionResource::getUrl('index', [
                    'tableFilters' => ['window' => ['value' => 'expiring']],
                ])),
            Stat::make('Expired', (string) $kpis['expired'])
                ->description('Past end date')
                ->chart($this->spark((int) $kpis['expired']))
                ->chartColor('danger')
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('gray')
                ->url(UserSubscriptionResource::getUrl('index', [
                    'tableFilters' => ['window' => ['value' => 'expired']],
                ])),
            Stat::make('Blocked users', (string) $kpis['blocked'])
                ->description('Cannot use the app')
                ->chart($this->spark((int) $kpis['blocked']))
                ->chartColor('gray')
                ->icon(Heroicon::OutlinedNoSymbol)
                ->color('gray')
                ->url(UserResource::getUrl('index', [
                    'tableFilters' => ['status' => ['value' => User::STATUS_BLOCKED]],
                ])),
            Stat::make('Videos', (string) $kpis['videos'])
                ->description($kpis['free_videos'].' free · '.$kpis['paid_videos'].' paid')
                ->chart($this->spark((int) $kpis['videos']))
                ->chartColor('success')
                ->icon(Heroicon::OutlinedFilm)
                ->color('gray')
                ->url(VideoResource::getUrl()),
            Stat::make('Deleted accounts', (string) $kpis['deleted'])
                ->description('Anonymized accounts')
                ->icon(Heroicon::OutlinedTrash)
                ->url(UserResource::getUrl('index', [
                    'tableFilters' => ['trashed' => ['value' => false]],
                ])),
        ];
    }

    /**
     * @return list<int>
     */
    private function spark(int $value): array
    {
        if ($value <= 0) {
            return [0, 0, 0, 0, 0, 0];
        }

        return [
            0,
            (int) round($value * 0.15),
            (int) round($value * 0.3),
            (int) round($value * 0.5),
            (int) round($value * 0.75),
            $value,
        ];
    }
}
