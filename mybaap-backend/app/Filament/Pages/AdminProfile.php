<?php

namespace App\Filament\Pages;

use App\Models\Admin;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rule;

class AdminProfile extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Profile';

    protected static ?string $slug = 'profile';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $admin = auth('admin')->user();
        $this->form->fill([
            'name' => $admin?->name,
            'email' => $admin?->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(80),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->rule(fn (): \Illuminate\Validation\Rules\Unique => Rule::unique('admins', 'email')->ignore(auth('admin')->id())),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->rule('nullable')
                    ->rule('min:8')
                    ->same('password_confirmation')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('profile-form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save profile')->submit('save'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        /** @var Admin $admin */
        $admin = auth('admin')->user();
        $admin->name = $state['name'];
        $admin->email = $state['email'];
        if (filled($state['password'] ?? null)) {
            $admin->password = $state['password'];
        }
        $admin->save();

        Notification::make()->title('Profile saved')->success()->send();
    }
}
