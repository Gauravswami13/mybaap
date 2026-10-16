<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Video;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class DashboardService
{
    public function kpis(): array
    {
        $today = today()->toDateString();

        return [
            'users' => User::query()->count(),
            'blocked' => User::query()->where('status', User::STATUS_BLOCKED)->count(),
            'deleted' => User::onlyTrashed()->count(),
            'active_subscriptions' => UserSubscription::query()
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'expiring' => UserSubscription::query()
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->whereDate('end_date', '>=', $today)
                ->whereDate('end_date', '<=', today()->addDays(7)->toDateString())
                ->count(),
            'expired' => UserSubscription::query()
                ->whereDate('end_date', '<', $today)
                ->where('status', '!=', UserSubscription::STATUS_CANCELLED)
                ->count(),
            'videos' => Video::query()->count(),
            'free_videos' => Video::query()->where('is_paid', false)->count(),
            'paid_videos' => Video::query()->where('is_paid', true)->count(),
            'plans' => SubscriptionPlan::query()->where('is_active', true)->count(),
        ];
    }

    public function revenue(): array
    {
        return [
            'total' => $this->money($this->sum()),
            'this_month' => $this->money($this->sum(now()->startOfMonth(), now()->endOfMonth())),
            'last_month' => $this->money($this->sum(now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth())),
            'total_raw' => $this->sum(),
            'this_month_raw' => $this->sum(now()->startOfMonth(), now()->endOfMonth()),
            'last_month_raw' => $this->sum(now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()),
        ];
    }

    public function months(): Collection
    {
        return UserSubscription::query()
            ->with('plan')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn (UserSubscription $row): string => $row->created_at?->format('Y-m') ?? 'unknown')
            ->map(function (Collection $rows, string $month): array {
                return [
                    'month' => $month,
                    'label' => $month === 'unknown' ? 'Unknown' : \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                    'subscriptions' => $rows->count(),
                    'revenue' => $this->money((float) $rows->sum(fn (UserSubscription $row): float => (float) ($row->plan?->price ?? 0))),
                ];
            })
            ->values();
    }

    public function report(string $period, string $category = 'all'): array
    {
        [$from, $to] = match ($period) {
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            '7' => [now()->subDays(6)->startOfDay(), now()],
            '30' => [now()->subDays(29)->startOfDay(), now()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };

        $category = in_array($category, ['all', 'free', 'paid'], true) ? $category : 'all';
        $subscriptions = UserSubscription::query()->with('plan')->whereBetween('created_at', [$from, $to])->get();
        if ($category === 'free') {
            $subscriptions = $subscriptions->filter(fn (UserSubscription $row): bool => (float) ($row->plan?->price ?? 0) <= 0)->values();
        } elseif ($category === 'paid') {
            $subscriptions = $subscriptions->filter(fn (UserSubscription $row): bool => (float) ($row->plan?->price ?? 0) > 0)->values();
        }

        $byPlan = $subscriptions
            ->groupBy(fn (UserSubscription $row): string => $row->plan?->name ?? 'Unknown')
            ->map(function (Collection $rows, string $name): array {
                $amount = (float) $rows->sum(fn (UserSubscription $row): float => (float) ($row->plan?->price ?? 0));

                return [
                    'plan' => $name,
                    'count' => $rows->count(),
                    'revenue' => $this->money($amount),
                    'revenue_raw' => $amount,
                    'type' => $amount > 0 ? 'Paid' : 'Free',
                ];
            })
            ->sortByDesc('revenue_raw')
            ->values();

        $freeVideos = Video::query()->where('is_paid', false)->count();
        $paidVideos = Video::query()->where('is_paid', true)->count();

        return [
            'from' => $from->format('d M Y'),
            'to' => $to->format('d M Y'),
            'new_users' => User::query()->whereBetween('created_at', [$from, $to])->count(),
            'subscriptions' => $subscriptions->count(),
            'revenue' => $this->money((float) $subscriptions->sum(fn (UserSubscription $row): float => (float) ($row->plan?->price ?? 0))),
            'free_videos' => $freeVideos,
            'paid_videos' => $paidVideos,
            'by_plan' => $byPlan,
            'charts' => [
                'weeks' => $this->signupSeries($from, $to),
                'plans' => [
                    'labels' => $byPlan->pluck('plan')->all(),
                    'values' => $byPlan->pluck('revenue_raw')->map(fn ($amount): float => (float) $amount)->all(),
                ],
                'mix' => [
                    'free' => $category === 'paid' ? 0 : $freeVideos,
                    'paid' => $category === 'free' ? 0 : $paidVideos,
                ],
            ],
        ];
    }

    /**
     * @return array{labels: array<int, string>, values: array<int, int>, axis: string}
     */
    private function signupSeries(CarbonInterface $from, CarbonInterface $to): array
    {
        $users = User::query()->whereBetween('created_at', [$from, $to])->get(['created_at']);
        $labels = [];
        $values = [];
        $byMonth = $from->diffInDays($to) > 62;
        $cursor = $byMonth ? $from->copy()->startOfMonth() : $from->copy()->startOfWeek();

        while ($cursor <= $to) {
            $end = $byMonth ? $cursor->copy()->endOfMonth() : $cursor->copy()->endOfWeek();
            $labels[] = $cursor->format($byMonth ? 'M' : 'd M');
            $values[] = $users->filter(
                fn (User $user): bool => $user->created_at !== null && $user->created_at->between($cursor, $end)
            )->count();
            $byMonth ? $cursor->addMonth() : $cursor->addWeek();
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'axis' => $byMonth ? 'Month' : 'Week',
        ];
    }

    public function money(float $amount): string
    {
        $symbol = AppSetting::getValue('currency_symbol', '₹') ?? '₹';
        $decimals = fmod($amount, 1.0) === 0.0 ? 0 : 2;

        return $symbol.number_format($amount, $decimals);
    }

    private function sum(?CarbonInterface $from = null, ?CarbonInterface $to = null): float
    {
        $query = UserSubscription::query()->with('plan');
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return (float) $query->get()->sum(fn (UserSubscription $row): float => (float) ($row->plan?->price ?? 0));
    }
}
