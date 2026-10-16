<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\RevenueMonths;
use App\Filament\Resources\UserSubscriptions\UserSubscriptionResource;
use App\Services\DashboardService;
use Filament\Widgets\Widget;

class RevenueOverview extends Widget
{
    protected static ?int $sort = 2;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.revenue-overview';

    protected function getViewData(): array
    {
        return [
            'revenue' => app(DashboardService::class)->revenue(),
            'links' => [
                'total' => UserSubscriptionResource::getUrl(),
                'this_month' => UserSubscriptionResource::getUrl('index', [
                    'tableFilters' => ['window' => ['value' => 'this_month']],
                ]),
                'last_month' => UserSubscriptionResource::getUrl('index', [
                    'tableFilters' => ['window' => ['value' => 'last_month']],
                ]),
                'months' => RevenueMonths::getUrl(),
            ],
        ];
    }
}
