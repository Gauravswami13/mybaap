<?php

namespace App\Filament\Pages;

use App\Services\DashboardService;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Reports extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'Reports';

    protected static ?string $slug = 'reports';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.reports';

    public string $period = 'this_month';

    public string $category = 'all';

    protected function getViewData(): array
    {
        return [
            'report' => app(DashboardService::class)->report($this->period, $this->category),
            'period' => $this->period,
            'category' => $this->category,
        ];
    }
}
