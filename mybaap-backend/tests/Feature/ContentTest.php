<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_and_faqs_hide_inactive_records(): void
    {
        Page::query()->create([
            'slug' => 'privacy-policy',
            'title' => 'Privacy Policy',
            'content' => '<p>We store your mobile number.</p>',
            'is_active' => true,
        ]);
        Page::query()->create([
            'slug' => 'terms',
            'title' => 'Terms',
            'content' => '<p>Hidden terms.</p>',
            'is_active' => false,
        ]);
        Faq::query()->create([
            'question' => 'Visible?',
            'answer' => 'Yes.',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Faq::query()->create([
            'question' => 'Hidden?',
            'answer' => 'No.',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $this->getJson('/api/v1/pages/privacy-policy')
            ->assertOk()
            ->assertJsonPath('data.title', 'Privacy Policy')
            ->assertJsonPath('data.slug', 'privacy-policy');

        $this->getJson('/api/v1/pages/terms')->assertNotFound();
        $this->getJson('/api/v1/pages/missing')->assertNotFound();

        $this->getJson('/api/v1/faqs')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.question', 'Visible?');

        $this->get('/privacy-policy')
            ->assertOk()
            ->assertSee('Privacy Policy')
            ->assertSee('We store your mobile number.');

        $this->get('/terms')->assertNotFound();
    }
}
