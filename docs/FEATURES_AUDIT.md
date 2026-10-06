# Features audit — Feestrack reference vs MyBaap

Scan date: 2026-10-06. Read-only. Koi reference code change nahi.

- Backend: `C:\Users\gaurav\Downloads\Feestrack.com` (custom PHP admin, Filament nahi)
- App: `C:\Users\gaurav\StudioProjects\feestrack` (Flutter, library + gym owner app)

**Verdict** current MyBaap PRD ke hisaab se hai (`docs/PRD.md` section 3 aur 8):

| Verdict | Matlab |
|---|---|
| **Haan** | MyBaap me ye feature ya uska pattern chahiye (OTP, banners, plans, user block, count dashboard, fixed theme). |
| **Nahi** | Library / gym / fees ka business, real payment gateway, extra roles, extra reports. PRD me bahar hai. |
| **Shayad** | Baad me kaam aa sakta hai (Play policy, logs, push), lekin abhi PRD me nahi. |

Feestrack admin Filament nahi hai. "Haan" ka matlab idea/pattern hai, PHP views copy nahi.

---

## 1. Admin panel (live nav)

Sidebar: `resources/views/layouts/admin.php`. Routes: `routes/web.php`.

### Auth, dashboard, profile

| Feature | Kahan hai | MyBaap |
|---|---|---|
| Admin login / logout (email + password, guest middleware) | `app/Http/Controllers/Admin/AuthController.php`, `app/Http/Middleware/AdminAuth.php`, `GuestAdmin.php`, `resources/views/auth/` | **Haan** — alag admin guard. MyBaap me Filament login already hai. |
| Platform dashboard (owners, subscriptions, revenue cards) | `app/Http/Controllers/Admin/B2bDashboardController.php`, `app/Services/B2bDashboardService.php`, `resources/views/b2b/` | **Haan** sirf counts (users, active subs, videos, free vs paid). Revenue/owner cards **Nahi**. |
| Admin profile (name, email, password) | `app/Http/Controllers/Admin/ProfileController.php`, `resources/views/profile/` | **Shayad** — Filament user edit kaafi hai. |
| Global search (owner name, mobile, library name) + Ctrl+K | `resources/views/layouts/admin.php` (form → `admin/clients`) | **Nahi** |
| Topbar ticket badge | `resources/views/layouts/admin.php` | **Nahi** |

### Settings (super admin only)

Screen: `resources/views/settings/index.php`. Save: `app/Http/Controllers/Admin/SettingController.php`. Model: `app/Models/Setting.php`. Brand helper: `app/Support/Brand.php`.

| Tab / feature | Kya karta hai | MyBaap |
|---|---|---|
| Branding — app name, tagline, primary/secondary/accent color, font, logo, favicon, splash, "apply to admin", live preview | Bootstrap API se app ko bhejta hai | **Shayad** — PRD me theme fixed hai (feestrack green + Lexend), naam MyBaap. Admin se color badalna scope me nahi. |
| General — support email, call, WhatsApp, website, about, address, currency, timezone | Contact + money display | **Shayad** support email/phone. Currency/timezone **Nahi** (app me fees nahi). |
| General — fee due day | Library fee calendar | **Nahi** |
| App control — maintenance mode + message | APIs block | **Shayad** |
| App control — force update, min/latest version, Play Store URL | Bootstrap se app force-update | **Shayad** (release ke baad). iOS URL **Nahi**. |
| App control — allow new registrations | OTP signup band kar sakte ho | **Shayad** |
| Feature flags — payments, attendance, notifications, tickets, announcements, banners | App screens hide | **Nahi** — ye Feestrack modules hain. |
| Payments — Razorpay/Stripe/PayU/PhonePe keys, webhook secret, online payment switch | Real gateway | **Nahi** — PRD me sirf payment stub/webhook. |
| Messaging — email on/off, SMTP host/user/pass | Email | **Nahi** |
| Messaging — SMS on/off + API key | SMS | **Shayad** — MyBaap me `SmsProviderInterface` hai; keys `.env` me, admin form me nahi. |
| Messaging — FCM project, service-account JSON, push on/off | Firebase push | **Nahi** abhi. Baad me chahiye to **Shayad**. |
| Legal — CMS pages (privacy, terms, refund, about) | `SettingController` + `app/Models/Page.php`, public routes `/privacy-policy`, `/terms` | **Shayad** — Play Store policy ke liye, PRD me nahi. |
| Legal — FAQ add on same screen | `FaqController` bhi alag se | **Shayad** |

Public branding endpoint: `GET /website-branding` (`SettingController`). **Nahi** for MyBaap app.

### Roles and staff

| Feature | Kahan hai | MyBaap |
|---|---|---|
| Roles: `super_admin` / `admin` vs limited staff | `app/Support/Access.php` (`isSuper`, `can`, `filterNav`) | **Nahi** — PRD: extra roles nahi. Ek admin guard kaafi. |
| Staff CRUD + per-vendor powers (members, enquiries, seats, trainers, plans, invoices, users, reports, notifications) | `app/Http/Controllers/Admin/StaffController.php`, `app/Models/VendorAdmin.php`, `resources/views/staff/` | **Nahi** |
| Vendor switch / vertical switch (library vs gym) | `VendorController.php`, `VerticalController.php`, `app/Support/Vendor.php`, `Vertical.php` | **Nahi** |

### Content jo MyBaap jaisa hai

| Feature | Kahan hai | MyBaap |
|---|---|---|
| Banners CRUD, enable/disable, image | `app/Http/Controllers/Admin/BannerController.php`, `app/Models/Banner.php`, `resources/views/banners/` | **Haan** |
| Onboarding slides (banner controller ke andar) | `BannerController` `storeSlide` / `destroySlide`, `app/Models/OnboardingSlide.php` | **Nahi** |
| Pop-ups (image, schedule, enable/disable) | `app/Http/Controllers/Admin/PopupController.php`, `app/Models/Popup.php`, `resources/views/popups/` | **Shayad** — promo dialog. PRD me sirf banner slider hai. |
| SaaS plans & offers — plan on/off, delete | `SubscriptionController::offers`, `togglePlan` | **Haan** plan enable/disable ke pattern ke liye. "Offers" pricing **Nahi**. |
| Owner subscriptions — list, change plan, status, manual create | `app/Http/Controllers/Admin/SubscriptionController.php`, `resources/views/` subscriptions via b2b | **Shayad** — MyBaap me user subscription manual activate/extend/cancel already hai. Ye B2B owner billing hai. |
| Payments list (gateway payments) | `SubscriptionController::payments`, `resources/views/payments/` | **Nahi** |

### Users, support, comms

| Feature | Kahan hai | MyBaap |
|---|---|---|
| App users list / create / edit / delete | `app/Http/Controllers/Admin/UserController.php`, `resources/views/users/` | **Haan** list + search + block/unblock. Create/delete user **Shayad**. |
| Account deletion requests (review, remove) | `app/Http/Controllers/Admin/AccountDeletionController.php`, `app/Models/AccountDeletionRequest.php`, `resources/views/account_deletions/` | **Shayad** — Play "delete account" ke liye. PRD me nahi. |
| Public web delete form | `app/Http/Controllers/AccountDeletionController.php`, `resources/views/account/` | **Shayad** |
| Notifications — send, device register | `app/Http/Controllers/Admin/NotificationController.php`, `app/Models/Notification.php`, `app/Services/PushService.php`, `resources/views/notifications/` | **Shayad** |
| Announcements CRUD | same controller, `app/Models/Announcement.php` | **Shayad** |
| Support tickets + reply | `app/Http/Controllers/Admin/TicketController.php`, `app/Models/Ticket.php`, `resources/views/tickets/` | **Shayad** |
| FAQs CRUD | `app/Http/Controllers/Admin/FaqController.php`, `app/Models/Faq.php`, `resources/views/faqs/` | **Shayad** |
| WhatsApp message templates | `app/Http/Controllers/Admin/MessageTemplateController.php`, `app/Models/MessageTemplate.php`, `app/Support/ReminderMessage.php`, `resources/views/message_templates/` | **Nahi** |
| Activity logs (last 200 admin actions) | `app/Http/Controllers/Admin/LogController.php`, `app/Models/ActivityLog.php`, `resources/views/logs/` | **Shayad** — debug ke liye useful, PRD me nahi. |
| In-admin API docs page | `app/Http/Controllers/Admin/ApiDocController.php`, `resources/views/api_docs/` | **Nahi** — MyBaap me `docs/API.md` hai. |

### Reports and export

| Feature | Kahan hai | MyBaap |
|---|---|---|
| Platform reports (date range, vendor filter) | `app/Http/Controllers/Admin/ReportController.php`, `resources/views/reports/` | **Nahi** — PRD: basic counts only, extra reports bahar. |
| Report PDF / print layout | `ReportController::pdf`, `resources/views/layouts/print.php`, `app/Support/SimplePdf.php` | **Nahi** |
| Report CSV/export | `ReportController::export`, `app/Support/SimpleXlsx.php` | **Nahi** |
| Export data (download + share link) | `app/Http/Controllers/Admin/ExportController.php`, `resources/views/export/` | **Nahi** |
| Per-owner invoice + vendor report PDF | `BusinessController` invoice/report/pdf | **Nahi** |

### Library / gym business (poora out)

| Feature | Kahan hai | MyBaap |
|---|---|---|
| Libraries / owners (clients) CRUD, status, extend, offer, password reset | `app/Http/Controllers/Admin/BusinessController.php`, `app/Models/Business.php` | **Nahi** |
| Seats, floors, shifts | `SeatController.php`, `app/Services/SeatOps.php`, `app/Models/Seat.php`, `Shift.php` | **Nahi** |
| Members CRUD, block, renew | `MemberController.php`, `app/Models/Member.php` | **Nahi** |
| Member fee plans | `MembershipPlanController.php`, `app/Models/MembershipPlan.php` | **Nahi** |
| Trainers | `TrainerController.php`, `app/Models/Trainer.php` | **Nahi** |
| Invoices + collect payment | `InvoiceController.php`, `app/Models/Invoice.php`, `app/Services/InvoiceShare.php`, `resources/views/invoice/` | **Nahi** |
| Enquiries / leads | `EnquiryController.php`, `app/Models/Enquiry.php` | **Nahi** |
| Attendance route | `routes/web.php` sirf dashboard par redirect karta hai | **Nahi** |

### Legacy files (live sidebar me nahi)

Views/controllers maujood hain, current `routes/web.php` me wired nahi. Sab **Nahi**.

| Area | Folder / controller |
|---|---|
| Students, courses, batches, fees, fee plans | `resources/views/students/`, `courses/`, `batches/`, `fees/`, `fee_plans/`; `StudentController.php`, `CourseController.php`, `BatchController.php`, `FeeController.php`, `FeePlanController.php` |
| Attendance, check-in | `AttendanceController.php`, `CheckInController.php`, `resources/views/attendance/` |
| Old dashboard | `DashboardController.php`, `app/Services/DashboardService.php`, `resources/views/dashboard/` |
| Old kernel | `routes/web.legacy.php`, `_legacy_src/`, `app/Http/Middleware/LegacyKernel.php` |

---

## 2. Flutter app

Routes: `lib/routes/routes.dart`, `lib/routes/route_generator.dart`.

### Auth and shell

| Feature | Kahan hai | MyBaap |
|---|---|---|
| Splash + token check | `lib/modules/auth/views/splash_view.dart` | **Haan** |
| Mobile login | `lib/modules/auth/views/login_view.dart` | **Haan** |
| OTP (6 digit, resend) | `lib/modules/auth/views/otp_view.dart`, `widgets/otp_input_widget.dart` | **Haan** |
| Choose management (library vs gym) | `lib/modules/onboarding/views/choose_management_view.dart` | **Nahi** |
| Business details + pincode lookup | `lib/modules/onboarding/views/business_details_view.dart`, `lib/services/location_lookup.dart` | **Nahi** |
| Setup seats | `lib/modules/onboarding/views/setup_seats_view.dart` | **Nahi** |
| Bottom nav shell + exit dialog | `lib/modules/dashboard/views/dashboard_view.dart`, `widgets/fab_bottom_appbar_item.dart` | **Shayad** shell pattern. MyBaap flow single Home hai, 4-tab owner app **Nahi**. |
| Push: device register on dashboard open | `lib/helper/push_notification_service.dart` | **Shayad** |
| Home popup dialog | `lib/modules/home/widgets/app_popup_dialog.dart`, `providers/popup_provider.dart` | **Shayad** |

### Home (owner dashboard, video home nahi)

| Feature | Kahan hai | MyBaap |
|---|---|---|
| Greeting, banner carousel, subscription expiry strip, seat/member stats | `lib/modules/home/views/home_view.dart`, `providers/home_provider.dart`, `models/dashboard_home_model.dart` | **Haan** sirf banner carousel pattern. Baaki stats/seats **Nahi**. |
| Members list, filter, add, details, search, block | `lib/modules/members/` | **Nahi** |
| Seats map, vacant/expired sheets, floors | `lib/modules/members/views/seats_view.dart` + seat widgets | **Nahi** |
| Member invoices (payment history of a member) | `lib/modules/members/views/invoice_list_view.dart` | **Nahi** |
| Collect fee bottom sheet | `lib/modules/members/widgets/collect_bottom_sheet.dart` | **Nahi** |

### Account = app settings hub

Screen: `lib/modules/account/views/account_view.dart`. Provider: `lib/modules/account/providers/account_provider.dart`.

| Menu item | Screen / action | MyBaap |
|---|---|---|
| Profile header (photo, name, plan line, expiry) | same file | **Haan** name + mobile. Photo upload **Shayad**. Plan expiry line **Haan** on profile/home. |
| Edit profile (name, photo, business fields) | `lib/modules/account/views/edit_profile_view.dart` | **Haan** name update (`PUT /profile`). Business/photo **Nahi**. |
| Plan management (owner ke member plans) | `lib/modules/plans/views/plan_management_view.dart`, `add_plan_view.dart` | **Nahi** — ye gym/library plans hain, app subscription plans nahi. |
| Renew membership | `lib/modules/plans/views/renew_membership_view.dart` | **Nahi** |
| Subscription (SaaS plans list + Razorpay) | `lib/modules/subscriptions/views/subscriptions_view.dart`, `lib/helper/razorpay_service.dart` | **Haan** plans list + current status UI. Razorpay checkout **Nahi** (placeholder button). |
| Purchase history | `lib/modules/subscriptions/views/subscription_transactions_view.dart` | **Shayad** — PRD me history screen nahi, sirf current subscription. |
| Enquiry | `lib/modules/enquiry/` | **Nahi** |
| WhatsApp templates + chat sheet | `lib/modules/whatsapp/views/whatsapp_templates_view.dart` | **Nahi** |
| Payment QR | `lib/modules/whatsapp/views/payment_qr_view.dart` | **Nahi** |
| Trainers (gym only) | `lib/modules/whatsapp/views/trainers_view.dart` | **Nahi** |
| Report (month-wise collection + detail sheet) | `lib/modules/reports/views/report_view.dart`, `widgets/month_details_bottom_sheet.dart` | **Nahi** |
| Export members sheet | `lib/modules/account/widgets/export_members_sheet.dart` | **Nahi** |
| Share app | `AccountProvider.shareApp` | **Shayad** |
| Notifications toggle | `account_view.dart` `_notificationToggle` | **Shayad** |
| Help & support | `lib/modules/help/views/help_support_view.dart` | **Shayad** |
| Tickets | `lib/modules/help/views/tickets_view.dart` | **Shayad** |
| Privacy / Terms / About (CMS) | `lib/modules/help/views/cms_page_view.dart` | **Shayad** |
| Chat with us (WhatsApp) | `AccountProvider.chatWithUs` | **Nahi** |
| Delete account | `lib/modules/account/widgets/delete_bottom_sheet_widget.dart` | **Shayad** |
| Logout confirm sheet | `lib/modules/account/widgets/logout_bottom_sheet_widget.dart` | **Haan** |

### History-style screens

| Screen | Kahan hai | MyBaap |
|---|---|---|
| Purchase history (owner ne kaunsa SaaS plan kharida) | `lib/modules/subscriptions/views/subscription_transactions_view.dart` | **Shayad** |
| Member invoice history | `lib/modules/members/views/invoice_list_view.dart` | **Nahi** |
| Monthly fee report | `lib/modules/reports/views/report_view.dart` | **Nahi** |
| Notification inbox (list, read, clear all) | `lib/modules/notifications/views/notification_view.dart`, `lib/helper/notification_navigator.dart` | **Shayad** |
| Ticket thread list | `lib/modules/help/views/tickets_view.dart` | **Shayad** |

### Theme (app me settings screen nahi — code + bootstrap)

| Piece | Kahan hai | MyBaap |
|---|---|---|
| Fixed palette, forest green `#058354` | `lib/core/color_constant.dart` | **Haan** — MyBaap yehi palette use karta hai. |
| Remote hex override helper | `ColorConstant.fromHex` | **Shayad** — admin branding se color aata hai. PRD me fixed theme hai. |
| Lexend / font styles | `lib/core/fonts_styles.dart` | **Haan** |
| Material theme (light only, dark mode nahi) | `lib/core/theme/app_theme.dart` | **Haan** light theme. Dark mode **Nahi** (Feestrack me bhi nahi). |
| Logo widget | `lib/core/common_widgets/feestrack_logo.dart`, `brand_header.dart` | **Haan** pattern; asset MyBaap ka. |
| Green app bar, buttons, inputs, toggles, empty/error | `lib/core/common_widgets/` | **Haan** reusable widgets. MyBaap me kuch already hain. |

### Networking (feature nahi, lekin app structure)

| Piece | Kahan hai | MyBaap |
|---|---|---|
| API client, result wrapper, URL constants | `lib/services/api_client.dart`, `api_services.dart`, `api_url.dart`, `api_result.dart` | **Haan** pattern. MyBaap Dio client already hai. |
| API debug logs | `lib/services/api_logs.dart` | **Shayad** sirf debug. |
| Session reset on 401 | `lib/services/session_reset.dart` | **Haan** |
| Razorpay service | `lib/helper/razorpay_service.dart` | **Nahi** |
| Image picker / camera | `lib/core/pick_image_utility.dart`, `media_source_picker.dart` | **Nahi** end-user ke liye. Video upload sirf admin. |

---

## 3. Short list — MyBaap me lene layak

**Haan (pattern already PRD me hai):** admin login, count dashboard, user list/block, banners, plan on/off, splash → mobile → OTP, profile name, logout, subscription plans screen (bina real gateway), fixed green theme + Lexend, banner carousel, loading/empty/error widgets.

**Shayad (abhi mat banao, baad me sochna):** maintenance + force update, activity logs, push/inbox, FAQ/CMS/privacy, delete-account, purchase history, home popup, share app.

**Nahi:** seats, members, fees, invoices, trainers, enquiries, WhatsApp, Razorpay keys, staff roles, PDF/Excel reports, library/gym onboarding, iOS.
