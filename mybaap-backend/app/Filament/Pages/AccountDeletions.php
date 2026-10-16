<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class AccountDeletions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTrash;

    protected static ?string $navigationLabel = 'Account deletions';

    protected static ?string $title = 'Account deletions';

    protected static ?string $slug = 'account-deletions';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.account-deletions';

    public function purge(int $id): void
    {
        $user = User::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($user): void {
            $user->tokens()->delete();
            $user->subscriptions()->delete();
            $user->forceDelete();
        });

        Notification::make()->title('Deleted account removed')->success()->send();
    }

    protected function getViewData(): array
    {
        return [
            'rows' => User::onlyTrashed()->latest('deleted_at')->get(),
        ];
    }
}
