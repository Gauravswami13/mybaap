<?php

namespace Tests\Feature;

use App\Models\Otp;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['otp.fixed_code' => '123456']);
    }

    public function test_delete_anonymizes_the_user_and_allows_the_mobile_to_register_again(): void
    {
        $user = User::factory()->create(['mobile' => '9123456780', 'name' => 'Gaurav']);
        $token = $user->createToken('android')->plainTextToken;
        Otp::query()->create([
            'mobile' => '9123456780',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
        ]);
        $plan = SubscriptionPlan::query()->create([
            'name' => 'Monthly',
            'price' => 99,
            'duration_days' => 30,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        UserSubscription::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'status' => UserSubscription::STATUS_ACTIVE,
            'payment_ref' => 'pay_keep',
        ]);

        $this->deleteJson('/api/v1/account')->assertUnauthorized();
        $this->withToken($token)->deleteJson('/api/v1/account', [])->assertUnprocessable();

        $this->withToken($token)
            ->deleteJson('/api/v1/account', ['confirm' => true])
            ->assertOk();

        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/profile')->assertUnauthorized();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('otps', ['mobile' => '9123456780']);
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'status' => UserSubscription::STATUS_CANCELLED,
            'payment_ref' => 'pay_keep',
        ]);
        $this->assertSame(1, User::onlyTrashed()->count());

        $this->postJson('/api/v1/auth/send-otp', ['mobile' => '9123456780'])->assertOk();
        $newId = $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => '9123456780',
            'otp' => '123456',
        ])->assertOk()->json('data.user.id');

        $this->assertNotSame($user->id, $newId);
        $this->get('/delete-account')->assertOk()->assertSee('Delete your MyBaap account');
    }
}
