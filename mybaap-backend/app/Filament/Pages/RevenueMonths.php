<?php

namespace App\Filament\Pages;

use App\Services\DashboardService;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class RevenueMonths extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Subscription';

    protected static ?string $navigationLabel = 'Revenue';

    protected static ?string $title = 'Month-wise revenue';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.revenue-months';

    protected function getViewData(): array
    {
        $dashboard = app(DashboardService::class);

        return [
            'months' => $dashboard->months(),
            'kpis' => $dashboard->kpis(),
            'revenue' => $dashboard->revenue(),
        ];
    }
}
