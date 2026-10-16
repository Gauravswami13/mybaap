<?php

namespace App\Services;

use App\Models\AppSetting;

class SettingsService
{
    public const SECRET_KEYS = [
        'pg_secret_key',
        'pg_webhook_secret',
        'smtp_pass',
        'sms_api_key',
        'fcm_server_key',
        'fcm_api_key',
        'fcm_service_account',
    ];

    public function publicSettings(): array
    {
        return [
            'app_share_text' => AppSetting::getValue(AppSetting::SHARE_TEXT, AppSetting::DEFAULT_SHARE_TEXT),
            'play_store_url' => AppSetting::getValue(AppSetting::PLAY_STORE_URL, AppSetting::DEFAULT_PLAY_STORE_URL),
        ];
    }

    public function bootstrap(): array
    {
        return [
            'app_name' => AppSetting::getValue('app_name', 'MyBaap'),
            'app_tagline' => AppSetting::getValue('app_tagline', 'Watch videos on MyBaap'),
            'primary_color' => AppSetting::color('primary_color', '#16a34a'),
            'secondary_color' => AppSetting::color('secondary_color', '#0d9488'),
            'accent_color' => AppSetting::color('accent_color', '#22c55e'),
            'app_font' => AppSetting::getValue('app_font', 'Lexend'),
            'support_email' => AppSetting::getValue(AppSetting::SUPPORT_EMAIL, '') ?? '',
            'support_phone' => AppSetting::getValue(AppSetting::SUPPORT_PHONE, '') ?? '',
            'website_url' => AppSetting::getValue(AppSetting::WEBSITE_URL, '') ?? '',
            'currency' => AppSetting::getValue(AppSetting::CURRENCY, 'INR'),
            'currency_symbol' => AppSetting::getValue(AppSetting::CURRENCY_SYMBOL, '₹'),
            'maintenance_mode' => AppSetting::flag('maintenance_mode'),
            'maintenance_message' => AppSetting::getValue('maintenance_message', 'App is under maintenance.'),
            'force_update' => AppSetting::flag('force_update'),
            'min_app_version' => AppSetting::getValue('min_app_version', '1.0.0'),
            'latest_app_version' => AppSetting::getValue('latest_app_version', '1.0.0'),
            'android_url' => AppSetting::getValue('android_url', AppSetting::DEFAULT_PLAY_STORE_URL),
            'force_update_message' => AppSetting::getValue('force_update_message', 'Please update the app to continue.'),
            'allow_registration' => AppSetting::flag('allow_registration', true),
            'features' => [
                'payments' => AppSetting::flag('feature_payments', true),
                'notifications' => AppSetting::flag('feature_notifications', true),
                'banners' => AppSetting::flag('feature_banners', true),
                'videos' => AppSetting::flag('feature_videos', true),
                'faqs' => AppSetting::flag('feature_faqs', true),
                'subscriptions' => AppSetting::flag('feature_subscriptions', true),
            ],
        ];
    }

    public function saveShare(string $text, ?string $url): void
    {
        AppSetting::putValue(AppSetting::SHARE_TEXT, $text);
        AppSetting::putValue(AppSetting::PLAY_STORE_URL, $url ?? '');
    }

    public function adminSettings(): array
    {
        return [
            'app_name' => AppSetting::getValue('app_name', 'MyBaap'),
            'app_tagline' => AppSetting::getValue('app_tagline', 'Watch videos on MyBaap'),
            'primary_color' => AppSetting::color('primary_color', '#16a34a'),
            'secondary_color' => AppSetting::color('secondary_color', '#0d9488'),
            'accent_color' => AppSetting::color('accent_color', '#22c55e'),
            'app_font' => AppSetting::getValue('app_font', 'Lexend'),
            'support_email' => AppSetting::getValue(AppSetting::SUPPORT_EMAIL, '') ?? '',
            'support_phone' => AppSetting::getValue(AppSetting::SUPPORT_PHONE, '') ?? '',
            'website_url' => AppSetting::getValue(AppSetting::WEBSITE_URL, '') ?? '',
            'currency' => AppSetting::getValue(AppSetting::CURRENCY, 'INR'),
            'currency_symbol' => AppSetting::getValue(AppSetting::CURRENCY_SYMBOL, '₹'),
            'timezone' => AppSetting::getValue('timezone', 'Asia/Kolkata'),
            'maintenance_mode' => AppSetting::flag('maintenance_mode'),
            'maintenance_message' => AppSetting::getValue('maintenance_message', 'App is under maintenance.'),
            'force_update' => AppSetting::flag('force_update'),
            'min_app_version' => AppSetting::getValue('min_app_version', '1.0.0'),
            'latest_app_version' => AppSetting::getValue('latest_app_version', '1.0.0'),
            'android_url' => AppSetting::getValue('android_url', AppSetting::DEFAULT_PLAY_STORE_URL),
            'force_update_message' => AppSetting::getValue('force_update_message', 'Please update the app to continue.'),
            'allow_registration' => AppSetting::flag('allow_registration', true),
            'feature_payments' => AppSetting::flag('feature_payments', true),
            'feature_notifications' => AppSetting::flag('feature_notifications', true),
            'feature_banners' => AppSetting::flag('feature_banners', true),
            'feature_videos' => AppSetting::flag('feature_videos', true),
            'feature_faqs' => AppSetting::flag('feature_faqs', true),
            'feature_subscriptions' => AppSetting::flag('feature_subscriptions', true),
            'allow_online_payment' => AppSetting::flag('allow_online_payment'),
            'pg_provider' => AppSetting::getValue('pg_provider', 'none'),
            'pg_public_key' => AppSetting::getValue('pg_public_key', '') ?? '',
            'email_notifications' => AppSetting::flag('email_notifications'),
            'sms_notifications' => AppSetting::flag('sms_notifications'),
            'push_enabled' => AppSetting::flag('push_enabled'),
            'smtp_host' => AppSetting::getValue('smtp_host', '') ?? '',
            'smtp_user' => AppSetting::getValue('smtp_user', '') ?? '',
            'fcm_project_id' => AppSetting::getValue('fcm_project_id', '') ?? '',
            'fcm_package_name' => AppSetting::getValue('fcm_package_name', 'com.mybaap.mybaap'),
            'app_share_text' => AppSetting::getValue(AppSetting::SHARE_TEXT, AppSetting::DEFAULT_SHARE_TEXT),
            'play_store_url' => AppSetting::getValue(AppSetting::PLAY_STORE_URL, AppSetting::DEFAULT_PLAY_STORE_URL),
        ];
    }

    public function saveAdmin(array $settings): void
    {
        $flags = [
            'maintenance_mode', 'force_update', 'allow_registration',
            'feature_payments', 'feature_notifications', 'feature_banners', 'feature_videos', 'feature_faqs', 'feature_subscriptions',
            'allow_online_payment', 'email_notifications', 'sms_notifications', 'push_enabled',
        ];
        $plain = [
            'app_name', 'app_tagline', 'primary_color', 'secondary_color', 'accent_color', 'app_font',
            'support_email', 'support_phone', 'website_url', 'currency', 'currency_symbol', 'timezone',
            'maintenance_message', 'min_app_version', 'latest_app_version', 'android_url', 'force_update_message',
            'pg_provider', 'pg_public_key', 'smtp_host', 'smtp_user', 'fcm_project_id', 'fcm_package_name',
        ];

        foreach ($plain as $key) {
            AppSetting::putValue($key, (string) ($settings[$key] ?? ''));
        }
        foreach ($flags as $key) {
            AppSetting::putValue($key, ! empty($settings[$key]) ? '1' : '0');
        }
        foreach (self::SECRET_KEYS as $key) {
            if (filled($settings[$key] ?? null)) {
                AppSetting::putValue($key, (string) $settings[$key]);
            }
        }

        $this->saveShare((string) ($settings['app_share_text'] ?? AppSetting::DEFAULT_SHARE_TEXT), $settings['play_store_url'] ?? '');
    }
}
