<?php

namespace App\Filament\Pages;

use App\Models\AppNotification;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SendNotification extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $title = 'Notifications';

    protected static ?string $slug = 'notifications';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.notifications';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'audience' => 'all',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('audience')
                    ->options([
                        'all' => 'All users',
                        'one' => 'One user',
                    ])
                    ->required()
                    ->live(),
                Select::make('user_id')
                    ->label('User')
                    ->options(fn (): array => User::query()->orderBy('mobile')->pluck('mobile', 'id')->all())
                    ->searchable()
                    ->visible(fn ($get): bool => $get('audience') === 'one')
                    ->required(fn ($get): bool => $get('audience') === 'one'),
                TextInput::make('title')->required()->maxLength(120),
                Textarea::make('body')->label('Message')->required()->rows(5),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('notification-form')
                ->livewireSubmitHandler('send')
                ->footer([
                    Actions::make([
                        Action::make('send')->label('Save notification')->submit('send'),
                    ]),
                ]),
        ]);
    }

    public function send(): void
    {
        $state = $this->form->getState();
        AppNotification::query()->create([
            'title' => $state['title'],
            'body' => $state['body'],
            'audience' => $state['audience'],
            'user_id' => $state['audience'] === 'one' ? $state['user_id'] : null,
        ]);
        $this->form->fill(['audience' => 'all', 'title' => '', 'body' => '']);

        Notification::make()
            ->title('Notification saved')
            ->body('It is stored in the admin inbox. Phone push waits until Firebase is configured under Settings → Messaging.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'items' => AppNotification::query()->with('user')->latest()->limit(20)->get(),
        ];
    }
}
