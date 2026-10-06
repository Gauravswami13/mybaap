<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\RelationManagers\SubscriptionsRelationManager;
use App\Filament\Resources\Users\UserResource;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_is_paginated_and_limited_to_the_signed_in_user(): void
    {
        $plan = SubscriptionPlan::query()->create([
            'name' => 'Monthly',
            'price' => 99,
            'duration_days' => 30,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $user = User::factory()->create();
        $other = User::factory()->create();
        $older = $this->subscription($user, $plan, 'pay_old');
        $newer = $this->subscription($user, $plan, 'pay_new');
        $this->subscription($other, $plan, 'pay_other');

        $this->getJson('/api/v1/subscription/history')->assertUnauthorized();

        $page = $this->actingAs($user)
            ->getJson('/api/v1/subscription/history?per_page=1')
            ->assertOk()
            ->json('data');

        $this->assertSame(1, $page['pagination']['current_page']);
        $this->assertSame(2, $page['pagination']['last_page']);
        $this->assertSame(2, $page['pagination']['total']);
        $this->assertCount(1, $page['items']);
        $this->assertSame($newer->id, $page['items'][0]['id']);
        $this->assertSame('Monthly', $page['items'][0]['plan_name']);
        $this->assertSame('99.00', $page['items'][0]['price']);
        $this->assertSame('pay_new', $page['items'][0]['payment_ref']);
        $this->assertNotSame($older->id, $page['items'][0]['id']);

        $second = $this->actingAs($user)
            ->getJson('/api/v1/subscription/history?per_page=1&page=2')
            ->assertOk()
            ->json('data.items.0.id');
        $this->assertSame($older->id, $second);

        $this->assertContains(SubscriptionsRelationManager::class, UserResource::getRelations());
    }

    private function subscription(User $user, SubscriptionPlan $plan, string $ref): UserSubscription
    {
        return UserSubscription::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'status' => UserSubscription::STATUS_ACTIVE,
            'payment_ref' => $ref,
        ]);
    }
}
