<?php

namespace Tests\Feature;

use App\Filament\Widgets\DashboardStats;
use App\Models\Admin;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_open_dashboard(): void
    {
        $admin = Admin::query()->create([
            'name' => 'MyBaap Admin',
            'email' => 'admin@mybaap.local',
            'password' => 'Admin@12345',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk()
            ->assertSee('Users')
            ->assertSee('Videos');

        Livewire::actingAs($admin, 'admin')
            ->test(DashboardStats::class)
            ->assertSee('Active subscriptions')
            ->assertSee('Total users')
            ->assertSee('Active plans');

        $this->actingAs($admin, 'admin')->get('/admin/users')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin/videos')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin/user-subscriptions')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin/categories')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin')->assertSee('Revenue Overview')->assertSee('Recent users');
        $this->actingAs($admin, 'admin')->get('/admin/api-docs')
            ->assertOk()
            ->assertSee('MyBaap API')
            ->assertSee('/auth/send-otp')
            ->assertSee('/payments/razorpay/order')
            ->assertSee('/videos/{id}/playback');
        $this->actingAs($admin, 'admin')->get('/admin/profile')->assertOk()->assertSee('Save profile');
        $this->actingAs($admin, 'admin')->get('/admin/revenue-months')->assertOk()->assertSee('Month');
        $this->actingAs($admin, 'admin')->get('/admin/manage-app-settings')->assertOk()->assertSee('Branding')->assertSee('App control');
        $this->actingAs($admin, 'admin')->get('/admin/notifications')->assertOk()->assertSee('Save notification');
        $this->actingAs($admin, 'admin')->get('/admin/account-deletions')->assertOk()->assertSee('No deleted accounts');
        $this->actingAs($admin, 'admin')->get('/admin/reports')->assertOk()->assertSee('Revenue by plan');
    }
}
