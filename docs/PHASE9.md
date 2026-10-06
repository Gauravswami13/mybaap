# MyBaap: Phase 9 (Extra Features, client-approved)

Phase 1 se 8 complete hain. Ab sirf ye 4 features banao, **ek ke baad ek**, bina puche. Baaki "Shayad" features (maintenance, force update, activity logs, push inbox, home popup) **mat banao**.

## Rules (same as PRD + project.mdc)

- Feestrack ke architecture, admin management style aur theme follow karo (PRD section 13). Reference projects me kuch modify mat karo.
- Har feature ke liye order: **backend (migration, model, service, FormRequest, API Resource, route) -> admin screen (Filament) -> Flutter (model, repository, provider, screen) -> test -> commit**.
- API format wahi: `{ success, message, data }`. Prefix `/api/v1`.
- Har feature ke baad `docs/API.md` update karo aur `docs/PROGRESS.md` me "Phase 9" ke neeche tick karo.
- Commit message: `Phase 9.N done: <feature>`.
- Existing PRD ke business rules mat todna.

---

## 9.1 Static pages / FAQ (CMS): privacy policy, terms, FAQ

**Kyun:** Play Store ke liye privacy policy URL zaroori hai, aur proposal me legal pages ki baat hai.

**Backend**
- Table `pages`: id, slug (unique: `privacy-policy`, `terms`, `faq`), title, content (longText, HTML ya markdown), is_active, timestamps.
- Table `faqs`: id, question, answer, sort_order, is_active, timestamps.
- Seeder: `privacy-policy` aur `terms` ke placeholder content, 5 sample FAQs.
- API (auth ki zarurat nahi, public):
  - `GET /api/v1/pages/{slug}` -> title, content
  - `GET /api/v1/faqs` -> active FAQs sort_order se
- **Public web route** (browser me khulne ke liye, Play Console me ye URL dalega): `GET /privacy-policy` aur `GET /terms`, simple clean Blade page.

**Admin (Filament)**
- Pages: edit title/content (rich text editor), enable/disable. Slug edit na ho.
- FAQs: CRUD + sort + enable/disable.

**Flutter**
- Profile/Settings area me tiles: Privacy Policy, Terms, FAQ.
- Pages `flutter_html` ya `WebView` se content dikhao; FAQ expandable list (ExpansionTile).
- Loading/error/empty states.

---

## 9.2 Delete account

**Kyun:** Google Play policy: account banane wale app me account delete option hona chahiye.

**Backend**
- `DELETE /api/v1/account` (auth required). Body: `confirm: true`.
- Service: user ke saare tokens revoke, user ka personal data (name, mobile) anonymize/delete, `otps` delete. `user_subscriptions` records financial record ke liye rakh sakte ho par user se unlink/anonymize (soft delete ya `user_id` null-safe).
- Pehle SoftDeletes ya anonymize approach chuno aur PROGRESS.md me likho kaunsa chuna.
- Deleted user dobara usi mobile se naya account bana sake.

**Admin**
- Users list me deleted users alag filter ya hidden. Admin ko deletion dikhe (count ya log line).

**Flutter**
- Profile screen me "Delete Account" (red, neeche).
- Confirmation dialog: warning text likho ki ye permanent hai, aur subscription bhi khatam hogi. "Cancel" / "Delete".
- Success par token/secure storage clear karo aur Mobile Number screen par bhejo.

**Play Store note:** ek public web page `/delete-account` bhi banao jisme instructions likhe hon (Play Console "Data safety" form me ye URL lagta hai).

---

## 9.3 Purchase history (subscription history)

**Backend**
- `GET /api/v1/subscription/history` (auth) -> paginated list, latest pehle.
- Item: plan name, price, start_date, end_date, status (active/expired/cancelled), payment_ref (nullable), created_at.
- Sirf us user ka data (authorization check).

**Admin**
- User Subscriptions list me already hai; user detail page par us user ki history table add karo.

**Flutter**
- Subscription screen ya Profile me "Purchase History" tile.
- List cards: plan name, date range, status badge (Active green, Expired grey, Cancelled red).
- Empty state: "Abhi tak koi subscription nahi", Plans screen ka button.
- Pagination (infinite scroll ya Load more).

---

## 9.4 Share app

**Backend**
- `app_settings` table (key/value) agar pehle se nahi hai: `app_share_text`, `play_store_url`.
- `GET /api/v1/settings/public` -> `app_share_text`, `play_store_url`.

**Admin**
- Settings screen me ye 2 fields edit karne ka option.

**Flutter**
- Profile/Home menu me "Share App" tile.
- `share_plus` package use karo: text = `app_share_text` + `play_store_url`.
- API fail ho to default text se fallback.

---

## Final checklist (Phase 9 ke end me)

- [ ] 9.1 Pages/FAQ: backend, admin, Flutter, public `/privacy-policy` aur `/terms` URL chalte hain
- [ ] 9.2 Delete account: API + Flutter flow + `/delete-account` public page
- [ ] 9.3 Purchase history: API + Flutter screen
- [ ] 9.4 Share app: settings API + admin + Flutter
- [ ] `docs/API.md` updated
- [ ] `flutter analyze` clean, `php artisan test` (jo tests hain) pass
- [ ] Release build dobara: `flutter build apk --release`
- [ ] PROGRESS.md me Phase 9 done likha

Sirf tab ruko jab user ka kuch chahiye ho (credentials, Play Store URL, content). Warna ek feature ke baad bina puche agla feature shuru karo.
