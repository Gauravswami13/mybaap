<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageAppSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Settings';

    protected static ?int $navigationSort = 20;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(app(SettingsService::class)->adminSettings());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->persistTab()
                    ->tabs([
                        Tab::make('Branding')->schema([
                            TextInput::make('app_name')->label('App name')->required()->maxLength(40),
                            TextInput::make('app_tagline')->label('Tagline')->maxLength(120),
                            ColorPicker::make('primary_color')->label('Primary color')->required(),
                            ColorPicker::make('secondary_color')->label('Secondary color')->required(),
                            ColorPicker::make('accent_color')->label('Accent color')->required(),
                            Select::make('app_font')->label('App font')->options([
                                'Lexend' => 'Lexend',
                                'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                            ])->required(),
                        ]),
                        Tab::make('General')->schema([
                            TextInput::make('support_email')->email()->maxLength(120),
                            TextInput::make('support_phone')->tel()->maxLength(20),
                            TextInput::make('website_url')->maxLength(255),
                            TextInput::make('currency')->required()->maxLength(8),
                            TextInput::make('currency_symbol')->label('Currency symbol')->required()->maxLength(4),
                            TextInput::make('timezone')->required()->maxLength(60),
                        ]),
                        Tab::make('App control')->schema([
                            Toggle::make('maintenance_mode')->label('Maintenance mode'),
                            Textarea::make('maintenance_message')->rows(2),
                            Toggle::make('force_update')->label('Force app update'),
                            TextInput::make('min_app_version')->maxLength(20),
                            TextInput::make('latest_app_version')->maxLength(20),
                            TextInput::make('android_url')->label('Android URL')->maxLength(255),
                            Textarea::make('force_update_message')->rows(2),
                            Toggle::make('allow_registration')->label('Allow new registrations'),
                        ]),
                        Tab::make('Features')->schema([
                            Toggle::make('feature_payments')->label('Payments'),
                            Toggle::make('feature_notifications')->label('Notifications'),
                            Toggle::make('feature_banners')->label('Home banners'),
                            Toggle::make('feature_videos')->label('Videos'),
                            Toggle::make('feature_faqs')->label('FAQs'),
                            Toggle::make('feature_subscriptions')->label('Subscriptions'),
                        ]),
                        Tab::make('Payments')->schema([
                            Toggle::make('allow_online_payment')->label('Allow online payments from the app'),
                            Select::make('pg_provider')->label('Provider')->options([
                                'none' => 'None',
                                'razorpay' => 'Razorpay',
                                'stripe' => 'Stripe',
                                'payu' => 'PayU',
                                'phonepe' => 'PhonePe',
                            ])->required(),
                            TextInput::make('pg_public_key')->label('Public key')->maxLength(255),
                            TextInput::make('pg_secret_key')->label('Secret key')->password()->revealable()->helperText('Leave blank to keep the saved key.'),
                            TextInput::make('pg_webhook_secret')->label('Webhook secret')->password()->revealable()->helperText('Leave blank to keep the saved secret.'),
                        ]),
                        Tab::make('Messaging')->schema([
                            Toggle::make('email_notifications')->label('Email notifications'),
                            Toggle::make('sms_notifications')->label('SMS notifications'),
                            Toggle::make('push_enabled')->label('Push notifications'),
                            TextInput::make('smtp_host')->maxLength(120),
                            TextInput::make('smtp_user')->maxLength(120),
                            TextInput::make('smtp_pass')->label('SMTP password')->password()->revealable()->helperText('Leave blank to keep the saved password.'),
                            TextInput::make('sms_api_key')->label('SMS API key')->password()->revealable()->helperText('Leave blank to keep the saved key.'),
                            TextInput::make('fcm_project_id')->label('FCM project ID')->maxLength(120),
                            TextInput::make('fcm_package_name')->label('Android package')->maxLength(120),
                            TextInput::make('fcm_api_key')->label('Firebase Android API key')->password()->revealable(),
                            Textarea::make('fcm_service_account')->label('Service account JSON')->rows(4)->helperText('Leave blank to keep the saved JSON. Phone push is not sent until this is set.'),
                        ]),
                        Tab::make('Share & legal')->schema([
                            Textarea::make('app_share_text')->label('Share text')->required()->maxLength(500),
                            TextInput::make('play_store_url')->label('Play Store URL')->maxLength(255),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('settings-form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save settings')->submit('save'),
                    ]),
                ]),
        ]);
    }

    public function save(SettingsService $settings): void
    {
        $settings->saveAdmin($this->form->getState());

        Notification::make()->title('Settings saved')->success()->send();
    }
}
