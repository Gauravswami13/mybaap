<?php

namespace App\Filament;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class GreenAvatarProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        $name = str(Filament::getNameForDefaultAvatar($record))->trim()->explode(' ');
        $initials = $name
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');

        if ($initials === '') {
            $initials = 'A';
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">'
            .'<rect width="64" height="64" rx="16" fill="#e50914"/>'
            .'<text x="32" y="41" text-anchor="middle" font-family="Inter,sans-serif" font-size="24" font-weight="700" fill="#ffffff">'
            .e($initials)
            .'</text></svg>';

        return 'data:image/svg+xml,'.rawurlencode($svg);
    }
}
