<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    public const SHARE_TEXT = 'app_share_text';

    public const PLAY_STORE_URL = 'play_store_url';

    public const DEFAULT_SHARE_TEXT = 'Watch videos on MyBaap.';

    public const DEFAULT_PLAY_STORE_URL = 'https://play.google.com/store/apps/details?id=com.mybaap.mybaap';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $value = static::query()->where('key', $key)->value('value');

        return $value === null || $value === '' ? $default : $value;
    }

    public static function putValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
