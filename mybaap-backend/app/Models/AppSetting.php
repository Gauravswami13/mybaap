<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    public const SHARE_TEXT = 'app_share_text';

    public const PLAY_STORE_URL = 'play_store_url';

    public const SUPPORT_EMAIL = 'support_email';

    public const SUPPORT_PHONE = 'support_phone';

    public const WEBSITE_URL = 'website_url';

    public const CURRENCY = 'currency';

    public const CURRENCY_SYMBOL = 'currency_symbol';

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

    public static function flag(string $key, bool $default = false): bool
    {
        $value = static::query()->where('key', $key)->value('value');
        if ($value === null || $value === '') {
            return $default;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    public static function color(string $key, string $default): string
    {
        $value = (string) static::getValue($key, $default);

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1 ? $value : $default;
    }
}
