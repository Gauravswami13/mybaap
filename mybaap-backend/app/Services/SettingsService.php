<?php

namespace App\Services;

use App\Models\AppSetting;

class SettingsService
{
    public function publicSettings(): array
    {
        return [
            'app_share_text' => AppSetting::getValue(AppSetting::SHARE_TEXT, AppSetting::DEFAULT_SHARE_TEXT),
            'play_store_url' => AppSetting::getValue(AppSetting::PLAY_STORE_URL, AppSetting::DEFAULT_PLAY_STORE_URL),
        ];
    }

    public function saveShare(string $text, ?string $url): void
    {
        AppSetting::putValue(AppSetting::SHARE_TEXT, $text);
        AppSetting::putValue(AppSetting::PLAY_STORE_URL, $url ?? '');
    }
}
