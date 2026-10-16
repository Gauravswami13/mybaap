<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_login_account_and_history(): void
    {
        $this->post('/login', ['mobile' => '9876543210'])
            ->assertRedirect(route('login.otp'));

        $this->post('/login/otp', ['mobile' => '9876543210', 'otp' => '123456'])
            ->assertRedirect(route('account'));

        $this->get('/account')->assertOk()->assertSee('9876543210');
        $this->get('/history')->assertOk()->assertSee('Subscription history');
        $this->get('/notifications')->assertOk();
        $this->get('/faq')->assertOk();
    }

    public function test_paid_video_page_hides_playback_path(): void
    {
        $category = Category::query()->create([
            'name' => 'Comedy',
            'slug' => 'comedy',
            'is_active' => true,
        ]);
        $video = Video::query()->create([
            'category_id' => $category->id,
            'title' => 'Paid Night',
            'playback_path' => 'videos/secret-file.mp4',
            'is_paid' => true,
            'is_active' => true,
        ]);

        $this->post('/login', ['mobile' => '9876543210']);
        $this->post('/login/otp', ['mobile' => '9876543210', 'otp' => '123456']);

        $this->get('/videos/'.$video->id)
            ->assertOk()
            ->assertSee('Get Subscription')
            ->assertDontSee('secret-file.mp4');

        $this->get('/watch/'.$video->id)
            ->assertRedirect(route('plans'));
    }
}
