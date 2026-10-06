# MyBaap

Android app for a paid video subscription. The API is Laravel, the admin panel is Filament, and the app is Flutter.

Laravel 12 is used because Composer blocks every Laravel 11 framework release for security advisories. The API shape, Sanctum auth, and Filament admin stay as specified in `docs/PRD.md`.

## Requirements

- PHP 8.3 (XAMPP’s PHP 8.1 cannot run this backend)
- Composer 2
- MySQL from XAMPP (user `root`, blank password)
- Flutter stable, Android SDK

On this machine PHP 8.3 is installed with winget. Put it and Composer on `PATH` before `php` or `composer` commands:

```powershell
$env:Path = "C:\Users\gaurav\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe;C:\Users\gaurav\tools;" + $env:Path
```

If MySQL crashes on startup with an InnoDB assertion, set `innodb_use_native_aio=0` under `[mysqld]` in `C:\xampp\mysql\bin\my.ini`, then start MySQL from the XAMPP control panel.

## Backend

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS mybaap_db CHARACTER SET utf8mb4;"
cd mybaap-backend
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan filament:assets
php artisan serve --host=0.0.0.0 --port=8000
```

Set `PAYMENT_WEBHOOK_SECRET` in `.env` before using `POST /api/v1/payments/webhook`. The header `X-Webhook-Signature` is HMAC-SHA256 of the raw body. Do not commit `.env`.

- API: `http://127.0.0.1:8000/api/v1` (see `docs/API.md`)
- Admin: `http://127.0.0.1:8000/admin`
- Seeded admin: `admin@mybaap.local` / `Admin@12345`
- Local OTP: `123456` (never used when `APP_ENV=production`)

`php artisan test` covers the admin panel and the API checks for auth, `playback_path`, and webhook signatures.

## Android app

```powershell
cd mybaap-app
flutter pub get
flutter run
```

The emulator default API base is `http://10.0.2.2:8000/api/v1`. For a physical phone on the same Wi-Fi:

```powershell
flutter run --dart-define=API_BASE_URL=http://YOUR_LAN_IP:8000/api/v1
```

Flow: splash, 10-digit mobile, 6-digit OTP, home. Free videos play. Paid videos with a lock open the subscription screen. Pay is a placeholder dialog. The server activates a plan only through the signed webhook.

Playback URLs are requested again every time a video opens and are not cached.

### Release build

`android/key.properties` and `android/keystore/*.jks` stay out of git. Create a keystore once:

```powershell
keytool -genkeypair -keystore android/keystore/mybaap-upload.jks -alias upload -keyalg RSA -keysize 2048 -validity 10000
```

`android/key.properties`:

```properties
storePassword=YOUR_STORE_PASSWORD
keyPassword=YOUR_KEY_PASSWORD
keyAlias=upload
storeFile=../keystore/mybaap-upload.jks
```

```powershell
flutter build apk --release
flutter build appbundle --release
```

Outputs:

- `mybaap-app/build/app/outputs/flutter-apk/app-release.apk`
- `mybaap-app/build/app/outputs/bundle/release/app-release.aab`
