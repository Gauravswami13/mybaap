# MyBaap API

Base URL: `http://127.0.0.1:8000/api/v1`

Run the API so a phone on the same network can reach it:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Every response uses this shape:

```json
{ "success": true, "message": "OK", "data": {} }
```

Errors use `success: false`, an HTTP status, and validation details in `data.errors`.

Protected routes need `Authorization: Bearer {token}` and return `403` when the user status is `blocked`.

`playback_path` is never returned. Playback is a 5-minute signed URL from `GET /videos/{id}/playback`.

Local and debug environments use fixed OTP `123456`. Production always sends a random code and never accepts the fixed code.

## Auth

### POST /auth/send-otp

No auth. Rate limit: 5 requests / 10 minutes per IP + mobile.

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
    "user": { "id": 1, "mobile": "9876543210", "name": null, "status": "active" }
  }
}
```

`401` invalid or expired OTP. `429` after 5 wrong attempts. `403` if the account is blocked.

### POST /auth/logout

Auth required. Revokes the current token.

```json
{ "success": true, "message": "Logged out.", "data": {} }
```

### GET /profile

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "user": { "id": 1, "mobile": "9876543210", "name": "Gaurav", "status": "active" },
    "subscription": null
  }
}
```

When a subscription is active, `subscription` includes `id`, `plan_id`, `plan_name`, `start_date`, `end_date`, `status`, `payment_ref`, `is_active`.

### PUT /profile

```json
{ "name": "Gaurav" }
```

Returns the same payload as GET /profile.

## Content

All content routes require auth. Inactive categories, videos, banners, and plans are omitted.

### GET /home

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "banners": [
      { "id": 1, "title": "Watch free demos", "image_url": "http://127.0.0.1:8000/storage/banners/banner-1.jpg", "link": null, "sort_order": 1 }
    ],
    "categories": [
      { "id": 1, "name": "Comedy", "slug": "comedy", "sort_order": 1 }
    ],
    "videos": [
      {
        "id": 1,
        "title": "Open Mic Clip",
        "description": "Open Mic Clip — sample catalog item.",
        "thumbnail_url": "http://127.0.0.1:8000/storage/thumbnails/video-1.jpg",
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

`data` is an array of active categories.

### GET /videos

Query: `category_id` (optional), `page` (optional).

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "videos": [],
    "pagination": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 0 }
  }
}
```

### GET /videos/{id}

`data` is one video object. `404` when the video or its category is inactive.

## Plans and subscription

### GET /plans

`data` is an array of active plans: `id`, `name`, `price`, `duration_days`, `description`, `sort_order`.

### GET /subscription

```json
{
  "success": true,
  "message": "OK",
  "data": { "is_active": false, "subscription": null }
}
```

## Playback

### GET /videos/{id}/playback

Auth required. Free videos, or paid videos with an active subscription, return:

```json
{
  "success": true,
  "message": "Playback URL ready.",
  "data": {
    "url": "http://127.0.0.1:8000/api/v1/media/videos/1?expires=1791301391&signature=...",
    "expires_in": 300
  }
}
```

Paid video without a subscription: `403`.

Open `data.url` directly in the player. It does not need the bearer token. A missing or edited signature returns `403`. The URL expires after 5 minutes.

## Payment webhook

### POST /payments/webhook

No bearer token. Header `X-Webhook-Signature` is HMAC-SHA256 of the raw body using `PAYMENT_WEBHOOK_SECRET`.

```json
{ "mobile": "9123456780", "plan_id": 1, "payment_ref": "pay_demo_1" }
```

`200` activates or extends the user's subscription. The same `payment_ref` is idempotent. `401` when the signature is wrong.

## Admin

Web panel: `http://127.0.0.1:8000/admin`

Seeded login: `admin@mybaap.local` / `Admin@12345`
