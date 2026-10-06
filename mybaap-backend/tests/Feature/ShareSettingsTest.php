<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageAppSettings;
use App\Models\Admin;
use App\Models\AppSetting;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShareSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_settings_can_be_read_and_saved_by_admin(): void
    {
        $this->getJson('/api/v1/settings/public')
            ->assertOk()
            ->assertJsonPath('data.app_share_text', AppSetting::DEFAULT_SHARE_TEXT)
            ->assertJsonPath('data.play_store_url', AppSetting::DEFAULT_PLAY_STORE_URL);

        $admin = Admin::query()->create([
            'name' => 'MyBaap Admin',
            'email' => 'admin@mybaap.local',
            'password' => 'Admin@12345',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($admin, 'admin')
            ->get('/admin/manage-app-settings')
            ->assertOk()
            ->assertSee('Share text');

        Livewire::actingAs($admin, 'admin')
            ->test(ManageAppSettings::class)
            ->fillForm([
                'app_share_text' => 'Share MyBaap with friends.',
                'play_store_url' => 'https://play.google.com/store/apps/details?id=com.mybaap.mybaap',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->getJson('/api/v1/settings/public')
            ->assertOk()
            ->assertJsonPath('data.app_share_text', 'Share MyBaap with friends.');
    }
}
