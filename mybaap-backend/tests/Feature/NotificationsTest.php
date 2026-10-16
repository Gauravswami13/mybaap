<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_sees_broadcasts_and_only_their_own_direct_messages(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        AppNotification::query()->create([
            'title' => 'Hello everyone',
            'body' => 'Broadcast',
            'audience' => 'all',
        ]);
        AppNotification::query()->create([
            'title' => 'Just for you',
            'body' => 'Direct',
            'audience' => 'one',
            'user_id' => $user->id,
        ]);
        AppNotification::query()->create([
            'title' => 'Someone else',
            'body' => 'Private',
            'audience' => 'one',
            'user_id' => $other->id,
        ]);

        $this->getJson('/api/v1/notifications')->assertUnauthorized();

        $titles = $this->actingAs($user)
            ->getJson('/api/v1/notifications')
            ->assertOk()
            ->json('data.*.title');

        $this->assertEqualsCanonicalizing(['Hello everyone', 'Just for you'], $titles);
    }
}
