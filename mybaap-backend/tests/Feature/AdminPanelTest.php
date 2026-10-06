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
            ->assertSee('Free videos')
            ->assertSee('Paid videos');

        $this->actingAs($admin, 'admin')->get('/admin/users')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin/videos')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin/user-subscriptions')->assertOk();
        $this->actingAs($admin, 'admin')->get('/admin/categories')->assertOk();
    }
}
