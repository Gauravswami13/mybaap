# MyBaap: Product Requirements (PRD)

Secure paid-video subscription platform. **Sirf Android** (iOS scope me nahi).

## 1. Stack

| Layer | Technology |
|---|---|
| Mobile app | Flutter / Dart (Android release), Provider state management |
| Backend | Laravel 11 (PHP 8.2+) REST API, Sanctum token auth |
| Admin panel | Laravel web admin (Filament) |
| Database | MySQL |
| Auth | Mobile number + OTP |
| Video security | Protected API, authorization checks, short-lived signed playback URL |

## 2. Scope (In)

- Flutter Android app
- Laravel REST APIs + business logic
- Laravel web admin panel
- MySQL schema + integration
- OTP auth + subscription/payment **hooks** (real gateway nahi, stub/webhook endpoint)
- Testing, release-ready APK/AAB

## 3. Scope (Out)

- iOS
- Public user video upload (content sirf admin manage karega)
- Real payment gateway onboarding, real SMS provider setup (client karega; interface ready rahega)
- Naye roles / extra reports / extra integrations

## 4. User App Flow

`Splash -> Mobile Number -> OTP Verification -> Home`

- Splash: token check. Valid token to seedha Home, warna Mobile screen.
- Mobile screen: 10 digit number, send OTP.
- OTP screen: 6 digit, resend timer (30s), verify.
- Home:
  - Profile section (name/number)
  - Promotional banner slider
  - Category chips: Comedy, Motivation/Speech, Local Movie, Stage Show (admin-managed, dynamic)
  - Video listing (category-wise filter)
- Video card: thumbnail, title, category, duration, Free/Paid badge, lock icon agar paid aur subscription nahi.
- Free/demo video: seedha play.
- Paid video: lock dikhe. Active subscription ho to unlock, warna Subscription screen par redirect.
- Subscription screen: active plans list, current subscription status, payment button (placeholder/hook).

## 5. Data Model

**users**: id, mobile (unique), name (nullable), status (active/blocked), timestamps

**otps**: id, mobile, code_hash, expires_at, attempts, verified_at, timestamps

**categories**: id, name, slug, sort_order, is_active, timestamps

**videos**: id, category_id, title, description, thumbnail (path), playback_path (private, kabhi API response me nahi), duration_seconds (nullable), is_paid (bool), is_active, sort_order, timestamps

**banners**: id, title, image, link (nullable), sort_order, is_active, timestamps

**subscription_plans**: id, name, price, duration_days, description, is_active, sort_order, timestamps

**user_subscriptions**: id, user_id, plan_id, start_date, end_date, status (active/expired/cancelled), payment_ref (nullable), timestamps

Indexes: users.mobile, videos(category_id, is_active), user_subscriptions(user_id, status, end_date).

## 6. API Contract

Base: `/api/v1`. Response format sab jagah:

```json
{ "success": true, "message": "OK", "data": {} }
```

Error: `success: false`, proper HTTP code, validation errors `data.errors` me.

| Method | Endpoint | Auth | Notes |
|---|---|---|---|
| POST | /auth/send-otp | No | mobile; rate limit 5/10min |
| POST | /auth/verify-otp | No | mobile, otp; returns token + user |
| POST | /auth/logout | Yes | token revoke |
| GET | /profile | Yes | user + active subscription |
| PUT | /profile | Yes | name update |
| GET | /home | Yes | banners, categories, videos |
| GET | /categories | Yes | active categories |
| GET | /videos | Yes | filter: category_id, page |
| GET | /videos/{id} | Yes | detail, `locked` flag |
| GET | /videos/{id}/playback | Yes | free ya active sub to short-lived signed URL, warna 403 |
| GET | /plans | Yes | active plans |
| GET | /subscription | Yes | current subscription status |
| POST | /payments/webhook | Signature | stub: subscription activate hook |

Video object (list/detail me): id, title, description, thumbnail_url, category, duration, is_paid, **locked** (bool). `playback_path` kabhi nahi.

## 7. Business Rules

- OTP: 6 digit, 5 min expiry, hashed store, max 5 wrong attempts, resend rate limit.
- Debug/local env me fixed OTP (e.g. 123456) allow, production me kabhi nahi.
- `locked = video.is_paid && !user.hasActiveSubscription()`.
- Active subscription = status active AND end_date >= today.
- Playback endpoint: auth + entitlement check, phir expiring signed URL (5 min). Direct permanent path kabhi expose nahi.
- Blocked user ke saare API calls 403.
- Disabled category/video/banner/plan API me nahi aayenge.

## 8. Admin Panel Modules

- Admin login (separate admin guard)
- Dashboard: total users, active subscriptions, total videos, free vs paid count
- Users: list, search, block/unblock
- Categories: CRUD + enable/disable + sort
- Videos: CRUD + thumbnail upload + Free/Paid toggle + enable/disable
- Banners: CRUD + enable/disable + sort
- Subscription Plans: CRUD + enable/disable
- User Subscriptions: list, manual activate/extend/cancel (payment gateway ke bina chalane ke liye)
- Basic reports: counts only

## 9. Security

- Sab content APIs Sanctum auth ke peeche
- Authorization checks har protected resource par
- Video storage private; sirf signed/expiring URL
- Validation FormRequest classes me
- Rate limiting auth endpoints par
- Screen recording ka absolute guarantee possible nahi (client ko pata hai)

## 10. Non-Functional

- Android min SDK 23+
- Pagination video lists par
- Loading, error, empty states har screen par
- Release signing config + AAB/APK build

## 11. Build Order

1. DB: migrations, models, seeders
2. Auth APIs
3. Content APIs
4. Subscription + secure playback
5. Admin panel
6. `docs/API.md` generate
7. Flutter: setup, auth, home, player, subscription, polish
8. Integration + security review + release build

---

## 12. AUTONOMOUS BUILD PLAN (Cursor ye follow kare)

Cursor ko ye poora plan **ek ke baad ek** khud execute karna hai. Progress `docs/PROGRESS.md` me track karo (checklist, `[ ]` -> `[x]`).

**Environment (Windows + XAMPP):**
- PHP/Composer terminal me available hain. MySQL XAMPP me chal raha hai (user `root`, password blank).
- Database banane ke liye: `C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS mybaap_db CHARACTER SET utf8mb4;"`
- Flutter already installed hai.

### Phase 1: Backend setup + DB
- [ ] `mybaap-backend` me Laravel 11 create karo, Sanctum install, `.env` DB config (`mybaap_db`)
- [ ] Migrations + models + relations + seeders (4 categories, 2 plans, 2 banners, 8 videos mix free/paid, 1 admin)
- [ ] `php artisan migrate --seed` pass

### Phase 2: Auth APIs
- [ ] send-otp, verify-otp, logout, profile (get/update)
- [ ] `SmsProviderInterface` + `LogSmsProvider`, fixed OTP 123456 local me
- [ ] Rate limit, FormRequests, ApiResponse helper

### Phase 3: Content APIs
- [ ] home, categories, videos list, video detail (`locked` flag), plans, subscription

### Phase 4: Subscription + secure playback
- [ ] Active-subscription service
- [ ] `GET /videos/{id}/playback` signed short-lived URL
- [ ] `POST /payments/webhook` stub

### Phase 5: Admin panel (Filament)
- [ ] Admin login, dashboard counts
- [ ] CRUD: categories, videos, banners, plans
- [ ] Users (block/unblock), user subscriptions (manual activate/extend/cancel)

### Phase 6: API docs
- [ ] `docs/API.md` generate karo (endpoints, request/response examples). Backend ko `php artisan serve --host=0.0.0.0` se test karke verify karo.

### Phase 7: Flutter app (`mybaap-app`)
- [ ] Project setup, folder structure, Dio + token interceptor, Provider, theme, routes
- [ ] Splash -> Mobile -> OTP -> Home
- [ ] Home: profile, banner slider, category chips, video list with lock
- [ ] Video player (free play, paid lock -> subscription screen)
- [ ] Subscription screen (plans + payment placeholder)
- [ ] Loading/error/empty states, `flutter analyze` clean

### Phase 8: QA + release
- [ ] Security review: auth bypass, `playback_path` leak, missing validations. Sab fix karo.
- [ ] Release signing config, `flutter build apk --release` / `appbundle`
- [ ] `README.md` me setup + run steps

### Working rules for this plan
- Ek phase khatam hone par: test karo, `docs/PROGRESS.md` update karo, `git commit -m "Phase N done"`, phir **bina puche agla phase shuru karo**.
- Sirf tab ruko jab: (a) user ka kuch chahiye (credentials, content, decision), ya (b) error 2 baar fix karne ke baad bhi na jaye. Tab clearly batao kya chahiye.
- Terminal commands khud chalao. Fail ho to error padho aur fix karo.

---

## 13. REFERENCE PROJECTS (sirf structure + theme ke liye)

Pehle se bane do projects reference ke liye hain. Inhe **padho aur unka style follow karo**, copy-paste mat karo.

- **Backend reference (Laravel):** `C:\Users\gaurav\Downloads\Feestrack.com`
  - Yahan se lo: folder/file architecture (controllers, services, requests, resources, routes organization), admin panel ka layout/management style, naming conventions.
- **Frontend reference (Flutter):** `C:\Users\gaurav\StudioProjects\feestrack`
  - Yahan se lo: folder structure, theme (colors, fonts, text styles, button/input styles), reusable widgets, Provider/state organization, networking setup.

Rules:
- Pehle dono projects ka structure scan karo (`lib/`, `app/`, `routes/`, `resources/views/` etc.), phir MyBaap ke liye same style me naya code likho.
- MyBaap ka business logic PRD ke hisaab se hi rahega (fees/school ka logic mat lana).
- Reference projects me **kuch modify mat karo** (read-only samjho).
- `.env`, passwords, API keys ya secrets reference se copy mat karo.
- Theme MyBaap ke liye feestrack jaisa rakho (same color palette/typography), app name aur branding MyBaap ka.
