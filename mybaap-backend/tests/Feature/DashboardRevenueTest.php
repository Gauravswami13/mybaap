<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRevenueTest extends TestCase
{
    use RefreshDatabase;

    public function test_revenue_uses_the_plan_price_of_subscriptions_in_each_month(): void
    {
        $plan = SubscriptionPlan::query()->create([
            'name' => 'Monthly',
            'price' => 100,
            'duration_days' => 30,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $user = User::factory()->create();
        $row = UserSubscription::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'status' => UserSubscription::STATUS_ACTIVE,
            'payment_ref' => 'pay_dash',
        ]);
        $row->forceFill(['created_at' => now()->startOfMonth()->addDay()])->save();

        $dashboard = app(DashboardService::class);

        $this->assertSame(100.0, $dashboard->revenue()['total_raw']);
        $this->assertSame(100.0, $dashboard->revenue()['this_month_raw']);
        $this->assertSame(0.0, $dashboard->revenue()['last_month_raw']);
        $this->assertSame('₹100', $dashboard->revenue()['total']);
        $this->assertCount(1, $dashboard->months());
    }
}
