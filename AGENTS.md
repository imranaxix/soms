# AGENTS.md

## What This Is

SOMS (Shop Order Management System) — a Laravel 13 B2B platform connecting shop owners with manufacturers. Three user roles: `shop_owner`, `manufacturer`, and `admin`.

## Quick Commands

```bash
composer setup          # Full fresh setup (install, env, migrate, npm, build)
composer dev            # Run all: server + queue + pail + vite (concurrent)
composer test           # Clear config cache → artisan test
php artisan serve       # Dev server only (port 8000)
```

No linting, typecheck, or formatting commands are configured. Laravel Pint is a dev dependency but has no script or config.

## Database

Default is SQLite. Tests use SQLite in-memory (see `phpunit.xml`). Migrations are in `database/migrations/`.

## Testing

PHPUnit 12 with two suites: `Unit` (tests/Unit) and `Feature` (tests/Feature). Tests include the `AdminPanelTest` feature suite (admin role + access control). Run the full suite:

```bash
composer test
```

Tests auto-clear config cache before running.

## Architecture

- **Roles**: `shop_owner`, `manufacturer`, and `admin` — enforced by `App\Http\Middleware\RoleMiddleware` (registered as `role` alias in `bootstrap/app.php`)
- **Routes**: `routes/web.php` only. No API routes file. Shop routes under `/shop/*`, manufacturer routes under `/manufacturer/*`, admin routes under `/admin/*`
- **Controllers**: `ShopOwnerController`, `ManufacturerController` (fat controllers — most logic lives here), plus `PaymentController`, `StripeConnectController`, `ConnectionController`, `AuthController`, `NotificationController`, `AdminController`
- **Models**: `User`, `Order`, `OrderStage`, `Payment`, `Product`, `ProductStage`, `ProductVariant`, `Connection`
- **Services**: `App\Services\JazzCashService` (JazzCash hash generation)
- **Views**: Blade templates in `resources/views/shop-owner/`, `resources/views/manufacturer/`, and `resources/views/admin/`, with shared layouts in `resources/views/layouts/`

## Payment Integrations

**JazzCash** (Pakistan mobile wallet):
- Uses v1.1 HTTP POST Page Redirect (not API v2.0) — see `config/jazzcash.php`
- Webhook callback route `/jazzcash/callback` is CSRF-exempt (see `bootstrap/app.php`)
- `JazzCashService` has hardcoded sandbox credentials — these are for testing only
- Sandbox testing requires ngrok tunnel; see README.md for full setup

**Stripe Connect** (international):
- Manufacturer onboarding via OAuth redirect flow
- Stripe confirm route is CSRF-exempt
- Uses `stripe/stripe-php` package

## Encrypted Fields

`User` model encrypts `jazzcash_password` and `jazzcash_integrity_salt` via Eloquent casts. These are never stored plaintext.

## Gotchas

- `scratch/` directory contains JazzCash hash debugging scripts — not application code
- JazzCash `pp_BillReference` must be <= 20 chars and different from `pp_TxnRefNo` (enforced in `JazzCashService:32`)
- Amounts are sent to JazzCash in paisas (amount * 100), not rupees
- `RoleMiddleware` redirects to the appropriate dashboard based on role — don't add generic auth checks where `role:` middleware already applies
- The `/` route serves the public landing page (`resources/views/landing.blade.php`)
- No CI workflows exist
- No `.env` file is committed (gitignored); copy `.env.example` as baseline
