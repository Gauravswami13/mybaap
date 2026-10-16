<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'otp.fixed_code' => '123456',
            'services.payment.webhook_secret' => 'test-webhook-secret',
        ]);
    }

    public function test_catalog_and_playback_stay_behind_auth_and_hide_the_file_path(): void
    {
        [$free, $paid] = $this->videos();
        Storage::disk('local')->put($free->playback_path, 'sample');
        Storage::disk('local')->put($paid->playback_path, 'sample');

        $this->getJson('/api/v1/home')->assertUnauthorized();
        $this->getJson('/api/v1/videos/'.$free->id.'/playback')->assertUnauthorized();
        $this->get('/api/v1/media/videos/'.$free->id)->assertForbidden();

        $token = $this->tokenFor('9123456780');

        $home = $this->withToken($token)->getJson('/api/v1/home')->assertOk();
        $home->assertJsonMissingPath('data.videos.0.playback_path');
        $this->assertStringNotContainsString($free->playback_path, $home->getContent());
        $this->assertStringNotContainsString($paid->playback_path, $home->getContent());

        $freePlay = $this->withToken($token)->getJson('/api/v1/videos/'.$free->id.'/playback')->assertOk();
        $this->assertStringNotContainsString('playback_path', $freePlay->getContent());
        $this->assertStringNotContainsString($free->playback_path, $freePlay->getContent());

        $url = parse_url($freePlay->json('data.url'));
        $this->get($url['path'].'?'.$url['query'])->assertOk();

        $this->withToken($token)
            ->getJson('/api/v1/videos/'.$paid->id.'/playback')
            ->assertForbidden();
    }

    public function test_active_subscription_unlocks_paid_playback(): void
    {
        [, $paid] = $this->videos();
        Storage::disk('local')->put($paid->playback_path, 'sample');
        $plan = SubscriptionPlan::query()->create([
            'name' => 'Monthly',
            'price' => 99,
            'duration_days' => 30,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $user = User::factory()->create(['mobile' => '9000000001']);
        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'status' => UserSubscription::STATUS_ACTIVE,
            'payment_ref' => 'pay_unlock',
        ]);
        $token = $user->createToken('android')->plainTextToken;

        $list = $this->withToken($token)
            ->getJson('/api/v1/videos?category_id='.$paid->category_id)
            ->assertOk();
        $row = collect($list->json('data.videos'))->firstWhere('id', $paid->id);
        $this->assertFalse($row['locked']);

        $this->withToken($token)->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('data.subscription.is_active', true);

        $this->withToken($token)
            ->getJson('/api/v1/videos/'.$paid->id.'/playback')
            ->assertOk()
            ->assertJsonPath('message', 'Playback URL ready.');
    }

    public function test_blocked_user_cannot_request_otp_or_use_an_existing_token(): void
    {
        $user = User::factory()->blocked()->create(['mobile' => '9876543210']);
        $token = $user->createToken('android')->plainTextToken;

        $this->postJson('/api/v1/auth/send-otp', ['mobile' => '9876543210'])
            ->assertForbidden();

        $this->withToken($token)->getJson('/api/v1/profile')->assertForbidden();
    }

    public function test_webhook_rejects_a_bad_signature_and_a_reused_payment_reference(): void
    {
        $plan = SubscriptionPlan::query()->create([
            'name' => 'Monthly',
            'price' => 99,
            'duration_days' => 30,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $owner = User::factory()->create(['mobile' => '9123456780']);
        $other = User::factory()->create(['mobile' => '9234567890']);

        $body = json_encode([
            'mobile' => $owner->mobile,
            'plan_id' => $plan->id,
            'payment_ref' => 'pay_shared',
        ], JSON_THROW_ON_ERROR);

        $this->call('POST', '/api/v1/payments/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => 'not-the-signature',
        ], $body)->assertUnauthorized();

        $this->call('POST', '/api/v1/payments/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => hash_hmac('sha256', $body, 'test-webhook-secret'),
        ], $body)->assertOk();

        $replay = json_encode([
            'mobile' => $other->mobile,
            'plan_id' => $plan->id,
            'payment_ref' => 'pay_shared',
        ], JSON_THROW_ON_ERROR);

        $this->call('POST', '/api/v1/payments/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => hash_hmac('sha256', $replay, 'test-webhook-secret'),
        ], $replay)->assertStatus(409);

        $this->assertDatabaseMissing('user_subscriptions', [
            'user_id' => $other->id,
        ]);
    }

    private function tokenFor(string $mobile): string
    {
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => $mobile])->assertOk();

        return $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => $mobile,
            'otp' => '123456',
        ])->assertOk()->json('data.token');
    }

    /**
     * @return array{0: Video, 1: Video}
     */
    private function videos(): array
    {
        $category = Category::query()->create([
            'name' => 'Comedy',
            'slug' => 'comedy',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $free = Video::query()->create([
            'category_id' => $category->id,
            'title' => 'Free clip',
            'playback_path' => 'videos/secret-free.mp4',
            'duration_seconds' => 10,
            'is_paid' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $paid = Video::query()->create([
            'category_id' => $category->id,
            'title' => 'Paid clip',
            'playback_path' => 'videos/secret-paid.mp4',
            'duration_seconds' => 10,
            'is_paid' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        return [$free, $paid];
    }
}
