<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageAppSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'App settings';

    protected static ?int $navigationSort = 20;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'app_share_text' => AppSetting::getValue(AppSetting::SHARE_TEXT, AppSetting::DEFAULT_SHARE_TEXT),
            'play_store_url' => AppSetting::getValue(AppSetting::PLAY_STORE_URL, AppSetting::DEFAULT_PLAY_STORE_URL),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('app_share_text')
                    ->label('Share text')
                    ->required()
                    ->maxLength(500),
                TextInput::make('play_store_url')
                    ->label('Play Store URL')
                    ->url()
                    ->maxLength(255),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('settings-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save')
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }

    public function save(SettingsService $settings): void
    {
        $state = $this->form->getState();
        $settings->saveShare($state['app_share_text'], $state['play_store_url'] ?? '');

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
