# MyBaap build progress

Laravel 12 is used because Composer blocks every Laravel 11 framework release for security advisories. API shape, Sanctum, and Filament stay as specified in the PRD.

Reference style: Feestrack backend layout (`Http/Controllers/Api`, `Services`, FormRequests, Resources) and feestrack Flutter theme (forest green `#058354`, Lexend).

## Phase 1: Backend setup + DB
- [x] `mybaap-backend` Laravel + Sanctum, `.env` DB `mybaap_db`
- [x] Migrations + models + relations + seeders (4 categories, 2 plans, 2 banners, 8 videos, 1 admin)
- [x] `php artisan migrate --seed` pass

## Phase 2: Auth APIs
- [x] send-otp, verify-otp, logout, profile (get/update)
- [x] `SmsProviderInterface` + `LogSmsProvider`, fixed OTP 123456 local me
- [x] Rate limit, FormRequests, ApiResponse helper

## Phase 3: Content APIs
- [x] home, categories, videos list, video detail (`locked` flag), plans, subscription

## Phase 4: Subscription + secure playback
- [x] Active-subscription service
- [x] `GET /videos/{id}/playback` signed short-lived URL
- [x] `POST /payments/webhook` stub

## Phase 5: Admin panel (Filament)
- [x] Admin login, dashboard counts
- [x] CRUD: categories, videos, banners, plans
- [x] Users (block/unblock), user subscriptions (manual activate/extend/cancel)

## Phase 6: API docs
- [x] `docs/API.md` generate karo (endpoints, request/response examples). Backend ko `php artisan serve --host=0.0.0.0` se test karke verify karo.

## Phase 7: Flutter app (`mybaap-app`)
- [ ] Project setup, folder structure, Dio + token interceptor, Provider, theme, routes
- [ ] Splash -> Mobile -> OTP -> Home
- [ ] Home: profile, banner slider, category chips, video list with lock
- [ ] Video player (free play, paid lock -> subscription screen)
- [ ] Subscription screen (plans + payment placeholder)
- [ ] Loading/error/empty states, `flutter analyze` clean

## Phase 8: QA + release
- [ ] Security review: auth bypass, `playback_path` leak, missing validations. Sab fix karo.
- [ ] Release signing config, `flutter build apk --release` / `appbundle`
- [ ] `README.md` me setup + run steps
