# MyBaap API

Base URL: `https://demo.exonex.ai/api/v1`

Every response uses this shape:

```json
{ "success": true, "message": "OK", "data": {} }
```

Errors use `success: false`, an HTTP status, and validation details in `data.errors`.

Protected routes need `Authorization: Bearer {token}` and return `403` when the user status is `blocked`.

`playback_path` is never returned. Playback is a 5-minute signed URL from `GET /videos/{id}/playback`.

While testing, the demo uses fixed OTP `123456`. When `APP_ENV=production`, the fixed code is refused.

## Auth

### POST /auth/send-otp

No auth. Rate limit: 5 requests / 10 minutes per IP + mobile. Body is JSON.

```json
{ "mobile": "9876543210" }
```

`200`

```json
{
  "success": true,
  "message": "OTP sent.",
  "data": { "mobile": "9876543210", "resend_in": 30 }
}
```

`422` when the number is not 10 digits starting with 6-9.

### POST /auth/verify-otp

No auth. `otp` is 6 digits. Local and demo testing accept `123456`.

```json
{ "mobile": "9876543210", "otp": "123456" }
```

`200`

```json
{
  "success": true,
  "message": "Verified successfully.",
  "data": {
    "token": "1|example",
    "user": {
      "id": 1,
      "mobile": "9876543210",
      "name": null,
      "photo_url": null,
      "status": "active"
    }
  }
}
```

Save `data.token`. Send it as `Authorization: Bearer TOKEN` on every protected call. `401` invalid or expired OTP. `429` after 5 wrong attempts. `403` if the account is blocked.

### POST /auth/logout

Auth required. No body. Revokes the current token.

```json
{ "success": true, "message": "Logged out.", "data": {} }
```

### GET /profile

Auth required. No body.

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "user": {
      "id": 1,
      "mobile": "9876543210",
      "name": "Gaurav",
      "photo_url": null,
      "status": "active"
    },
    "subscription": null
  }
}
```

When a subscription is active, `subscription` includes `id`, `plan_id`, `plan_name`, `price`, `start_date`, `end_date`, `status`, `payment_ref`, `created_at`, `is_active`.

### PUT /profile

Auth required. JSON body. Updates the display name.

```json
{ "name": "Gaurav" }
```

`200` returns the same payload as GET /profile.

### POST /profile

Auth required. Multipart form, not JSON. Use this when uploading a photo. PHP does not parse files on PUT.

Fields: `name` (required, max 80), `photo` (optional jpeg/png/webp, max 2 MB).

`200` returns the same payload as GET /profile. `photo_url` is a public storage URL after a photo is saved, otherwise `null`.

## Content

All content routes require auth. Inactive categories, videos, banners, and plans are omitted. A missing image is `null`; the app shows a placeholder.

### GET /home

Auth required. Banners, categories, and the latest videos for the home screen.

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "banners": [
      {
        "id": 1,
        "title": "Watch free demos",
        "image_url": "https://demo.exonex.ai/storage/banners/banner-1.jpg",
        "link": null,
        "sort_order": 1
      }
    ],
    "categories": [
      {
        "id": 1,
        "name": "Comedy",
        "slug": "comedy",
        "image_url": null,
        "sort_order": 1
      }
    ],
    "videos": [
      {
        "id": 1,
        "title": "Open Mic Clip",
        "description": "Open Mic Clip — sample catalog item.",
        "thumbnail_url": "https://demo.exonex.ai/storage/thumbnails/video-1.jpg",
        "category": { "id": 1, "name": "Comedy", "slug": "comedy" },
        "duration": 180,
        "is_paid": false,
        "locked": false
      }
    ]
  }
}
```

`locked` is `true` when the video is paid and the user has no active subscription (`status = active` and `end_date >= today`).

### GET /categories

Auth required. `data` is an array of active categories.

```json
{
  "success": true,
  "message": "OK",
  "data": [
    { "id": 1, "name": "Comedy", "slug": "comedy", "image_url": null, "sort_order": 1 }
  ]
}
```

### GET /videos

Auth required. Query: `category_id` (optional), `page` (optional).

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "videos": [
      {
        "id": 1,
        "title": "Open Mic Clip",
        "description": "Sample clip.",
        "thumbnail_url": null,
        "category": { "id": 1, "name": "Comedy", "slug": "comedy" },
        "duration": 180,
        "is_paid": true,
        "locked": true
      }
    ],
    "pagination": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1 }
  }
}
```

### GET /videos/{id}

Auth required. `data` is one video object, same fields as a home video. `404` when the video or its category is inactive.

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "title": "Open Mic Clip",
    "description": "Sample clip.",
    "thumbnail_url": null,
    "category": { "id": 1, "name": "Comedy", "slug": "comedy" },
    "duration": 180,
    "is_paid": false,
    "locked": false
  }
}
```

## Plans and subscription

Auth required.

### GET /plans

`data` is an array of active plans.

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "name": "Monthly",
      "price": 99,
      "duration_days": 30,
      "description": "30 days of paid videos",
      "sort_order": 1
    }
  ]
}
```

### GET /subscription

```json
{
  "success": true,
  "message": "OK",
  "data": { "is_active": false, "subscription": null }
}
```

When active, `subscription` has `id`, `plan_id`, `plan_name`, `price`, `start_date`, `end_date`, `status`, `payment_ref`, `created_at`, `is_active`.

### GET /subscription/history

Paginated, latest first, only the signed-in user's rows. Query: `page`, `per_page` (1–50, default 15).

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "items": [
      {
        "id": 1,
        "plan_id": 1,
        "plan_name": "Monthly",
        "price": "99.00",
        "start_date": "2026-10-06",
        "end_date": "2026-11-05",
        "status": "active",
        "payment_ref": "pay_1",
        "created_at": "2026-10-06T12:00:00+00:00",
        "is_active": true
      }
    ],
    "pagination": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1 }
  }
}
```

## Playback

### GET /videos/{id}/playback

Auth required. Free videos, or paid videos with an active subscription, return a short-lived URL. Do not cache it. Fetch a fresh URL every time the player opens.

```json
{
  "success": true,
  "message": "Playback URL ready.",
  "data": {
    "url": "https://demo.exonex.ai/api/v1/media/videos/1?expires=1791301391&signature=...",
    "expires_in": 300
  }
}
```

Paid video without a subscription: `403`. The file path is never included.

### GET /media/videos/{id}

No bearer token. Open `data.url` from playback directly in the player. The query must keep `expires` and `signature`. A missing or edited signature returns `403`. The URL expires after 5 minutes. This route streams the file; it is not a JSON envelope.

## Razorpay test checkout

Auth required. Keys come from `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET` in `.env`. If either key is empty the API returns `503` with message `Razorpay test keys are not configured.` Amount under 100 paise returns `422`.

### POST /payments/razorpay/order

```json
{ "plan_id": 1 }
```

`200`

```json
{
  "success": true,
  "message": "Razorpay order created.",
  "data": {
    "key_id": "rzp_test_example",
    "order_id": "order_test",
    "amount": 9900,
    "currency": "INR",
    "plan_name": "Monthly"
  }
}
```

`amount` is paise. Pass `key_id`, `order_id`, and `amount` to the Razorpay checkout. `422` when the plan is missing or inactive.

### POST /payments/razorpay/verify

Call this after checkout success. The server checks the HMAC of `order_id|payment_id`, then confirms the payment and order notes match this user and plan.

```json
{
  "plan_id": 1,
  "razorpay_order_id": "order_test",
  "razorpay_payment_id": "pay_test",
  "razorpay_signature": "hmac"
}
```

`200`

```json
{
  "success": true,
  "message": "Subscription activated.",
  "data": {
    "id": 1,
    "plan_id": 1,
    "plan_name": "Monthly",
    "price": "99.00",
    "start_date": "2026-10-08",
    "end_date": "2026-11-07",
    "status": "active",
    "payment_ref": "pay_test",
    "created_at": "2026-10-08T12:00:00+00:00",
    "is_active": true
  }
}
```

`401` bad signature. `422` amount, plan, or user mismatch. `502` when Razorpay cannot be reached.

## Payment webhook

### POST /payments/webhook

No bearer token. Header `X-Webhook-Signature` is HMAC-SHA256 of the raw body using `PAYMENT_WEBHOOK_SECRET`.

```json
{ "mobile": "9123456780", "plan_id": 1, "payment_ref": "pay_demo_1" }
```

`200` activates or extends the user's subscription. The same `payment_ref` is idempotent. `401` when the signature is wrong. This route stays available during maintenance mode.

## App bootstrap

### GET /app/bootstrap

No token. Public app settings only. Secrets such as payment keys and Firebase JSON are not included.

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "app_name": "MyBaap",
    "app_tagline": "Watch videos on MyBaap",
    "primary_color": "#16a34a",
    "secondary_color": "#0d9488",
    "accent_color": "#22c55e",
    "app_font": "Lexend",
    "support_email": "",
    "support_phone": "",
    "website_url": "",
    "currency": "INR",
    "currency_symbol": "₹",
    "maintenance_mode": false,
    "maintenance_message": "App is under maintenance.",
    "force_update": false,
    "min_app_version": "1.0.0",
    "latest_app_version": "1.0.0",
    "android_url": "https://play.google.com/store/apps/details?id=com.mybaap.mybaap",
    "force_update_message": "Please update the app to continue.",
    "allow_registration": true,
    "features": {
      "payments": true,
      "notifications": true,
      "banners": true,
      "videos": true,
      "faqs": true,
      "subscriptions": true
    }
  }
}
```

When `maintenance_mode` is on, other API calls return `503`. Bootstrap, public settings, and the payment webhook stay available.

## Public settings

### GET /settings/public

No token. Used by Share App.

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "app_share_text": "Watch videos on MyBaap.",
    "play_store_url": "https://play.google.com/store/apps/details?id=com.mybaap.mybaap"
  }
}
```

Admin edits both values at `/admin/manage-app-settings`.

## Notifications

### GET /notifications

Auth required. Latest 50 rows for this user: audience `all`, plus audience `one` only when `user_id` is the signed-in user. Another user's private notification is never included.

```json
{
  "success": true,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "title": "New video",
      "body": "A new clip is live.",
      "created_at": "2026-10-07T12:00:00+00:00"
    }
  ]
}
```

Admin composes these at `/admin/notifications`. Phone push is not sent until Firebase is configured.

## Account

### DELETE /account

Auth required. Blocked users can still delete their account.

```json
{ "confirm": true }
```

`200`

```json
{ "success": true, "message": "Account deleted.", "data": {} }
```

This revokes tokens, deletes OTPs for that mobile, cancels active subscriptions, anonymizes the name and mobile, and soft-deletes the user. The same mobile can register again. `422` when `confirm` is missing.

Play Console URL: `GET /delete-account` (instructions only, not under `/api/v1`).

## Pages and FAQ

Public. No bearer token. Disabled pages and FAQs are omitted.

### GET /pages/{slug}

Slugs: `privacy-policy`, `terms`.

```json
{
  "success": true,
  "message": "OK",
  "data": { "slug": "privacy-policy", "title": "Privacy Policy", "content": "<p>...</p>" }
}
```

`404` when the slug is missing or inactive.

Browser pages for Play Console: `GET /privacy-policy` and `GET /terms` (not under `/api/v1`).

### GET /faqs

```json
{
  "success": true,
  "message": "OK",
  "data": [
    { "id": 1, "question": "How do I subscribe?", "answer": "Open Plans and pay with Razorpay.", "sort_order": 1 }
  ]
}
```

## Admin

Web panel: `https://demo.exonex.ai/admin`

These are browser pages, not JSON APIs. Seeded login: `admin@mybaap.local` / `Admin@12345`.
