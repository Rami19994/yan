# Yanasib

> **This is an entertainment simulation platform only.** There is no real money, no real gambling, and no real prize payouts anywhere in this system. Every price, commission, and prize amount is a virtual value used purely for demonstration and simulation purposes.

Yanasib is a multi-tenant draw/raffle simulation built on Laravel 12 with a Clean Architecture layout (Domain / Application / Infrastructure / Presentation / Shared), three user roles (Admin, Master, Cashier), a full virtual ticket economy, and a cinematic Three.js + GSAP 3D draw engine with realtime spectator sync via Laravel Reverb.

## Requirements

- PHP 8.3+ (developed and tested against 8.4)
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8+ or MariaDB 10.4+
- Redis (optional — the app falls back to the `database` driver for cache/queue/session when Redis isn't available; see [Redis](#redis-optional))

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and point `DB_*` at your database (see [Database](#database) below), then:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Visit `http://localhost:8000` and log in with one of the [seeded demo accounts](#demo-accounts).

## Environment

Key `.env` settings beyond the Laravel defaults:

| Variable | Purpose |
|---|---|
| `APP_LOCALE` | Defaults to `ar` (Arabic, RTL). Set to `en` for English/LTR. |
| `APP_TIMEZONE` | Defaults to `Asia/Baghdad`; admin-configurable timezone is stored in the `settings` table. |
| `YANASIB_SIMULATION_MODE` | Always `true`. A reminder flag — this is a simulation, never wire this app to real payment rails. |
| `BROADCAST_CONNECTION` | `reverb` — see [Realtime / WebSockets](#realtime--websockets). |
| `REVERB_*` / `VITE_REVERB_*` | Reverb app credentials. Generate your own with `php artisan reverb:install` or set them manually; see comments in `.env.example`. |
| `REDIS_*` | Only used if you switch `CACHE_STORE`/`QUEUE_CONNECTION`/`SESSION_DRIVER` to `redis`. |

## Database

The app targets MySQL 8+/MariaDB 10.4+. Create a database and point `.env` at it:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yanasib
DB_USERNAME=root
DB_PASSWORD=
```

### Migration

```bash
php artisan migrate
```

All 7-digit ticket numbers are enforced unique at the database level (not just application-level) via a unique index, and ticket generation uses chunked `INSERT IGNORE` batches rather than per-row inserts — see `App\Domain\Ticket\Services\TicketNumberGenerator`.

### Seeder

```bash
php artisan db:seed
```

(or `migrate --seed` to do both at once.) This runs `RolesAndPermissionsSeeder` (Spatie roles/permissions for admin/master/cashier) and `YanasibDemoSeeder` (demo accounts below).

#### Demo accounts

All demo accounts use the password `password`.

| Role | Email |
|---|---|
| Admin | `admin@yanasib.test` |
| Master | `master1@yanasib.test`, `master2@yanasib.test` |
| Cashier | `cashier01@yanasib.test`, `cashier02@yanasib.test`, `cashier11@yanasib.test`, `cashier12@yanasib.test` |

The seeder creates the hierarchy (2 masters, 2 cashiers each) but does **not** create competitions/tickets — generate those from the Admin dashboard (**Competitions → New**, then **Generate Tickets** and **Assign to Master**) to try the full flow.

## Build Frontend

```bash
npm install
npm run dev    # local development, with HMR
npm run build  # production build
```

Vite builds two separate entry bundles:

- `resources/js/app.js` — loaded on every page (Tailwind, Alpine/Livewire wiring, Echo).
- `resources/js/draw.js` — the Three.js/GSAP 3D draw engine, loaded **only** on pages that render `<x-draw.stage>` (Draw Control Room, the public live draw screen). Per the project's own performance rule, this keeps the ~600KB Three.js bundle off every other page.

## Run Scheduler

```bash
php artisan schedule:work   # local dev
```

In production, point cron at `php artisan schedule:run` every minute (see [Laravel's scheduler docs](https://laravel.com/docs/scheduling#running-the-scheduler)).

## Realtime / WebSockets

The Admin Draw Control Room and the public `/draw/live/{competition}` spectator screen sync over Laravel Reverb — when an official draw completes, every open screen plays the identical wheel animation at the identical moment via a broadcast on a public `draw.{competitionId}` channel.

```bash
php artisan reverb:start
```

Reverb must be running for *live* sync between screens; without it, the public screen still correctly shows the final result on page load/refresh once a draw has completed (it just won't animate in real time for anyone already watching).

## Testing

```bash
php artisan test
```

126 tests cover role isolation across all three tiers, ticket generation/uniqueness, the full assignment chain, ticket sales (including a dedicated test that proves two concurrent sell attempts on the same ticket against the **real** MySQL/MariaDB connection can only ever have one winner — SQLite's locking semantics can't model this, so that one test intentionally bypasses the default test connection), the draw engine (random + admin-selected, redraw/no-winner policies, draw locking), the public verification flow, the REST API, audit logging, N+1 regressions, and security fixes.

Static analysis:

```bash
./vendor/bin/phpstan analyse
```

Code style:

```bash
./vendor/bin/pint
```

## API

A versioned REST API lives under `/api/v1`, authenticated via Laravel Sanctum tokens:

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"email":"admin@yanasib.test","password":"password","device_name":"my-device"}'
```

See `routes/api_v1.php` for the full endpoint list (auth, competitions, tickets, cashier sales, master/admin statistics and listings) — every endpoint is role-scoped server-side (never just a hidden frontend button) via the same Policies the web UI uses.

## Architecture

```
app/
  Domain/          Enums, Eloquent models, domain Exceptions, Policies, Events, domain Services — one folder per bounded context (User, Competition, Ticket, Customer, Sale, Assignment, Draw, Commission, Winner, Audit, Settings)
  Application/      UseCases, Queries, DTOs — orchestration, no framework/HTTP concerns
  Infrastructure/   Queue Jobs, Event Listeners — framework-facing glue
  Presentation/      Controllers, Livewire components, Form Requests, API Resources
  Shared/           Value Objects (Money, TicketNumber), base DomainException
```

Controllers and Livewire components stay thin — they validate input, call a UseCase, and render a response. Business logic (commission calculation, ticket assignment locking, the draw engine, double-sell prevention) lives in `Application/UseCases` and `Domain/*/Services`, which is also where almost all of the test suite's assertions point.

## Deploy

1. `composer install --no-dev --optimize-autoloader`
2. `npm ci && npm run build`
3. Set `APP_ENV=production` and `APP_DEBUG=false`
4. `php artisan migrate --force`
5. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
6. Run a queue worker (`php artisan queue:work` under a process supervisor) and `reverb:start` as long-running services
7. Point cron at `php artisan schedule:run` every minute
8. Serve over HTTPS — set `SESSION_SECURE_COOKIE=true` once you are

### Redis (optional)

Cache, queue, and session all default to the `database` driver so the app runs with nothing beyond MySQL. To use Redis instead, install the `phpredis` extension or `predis/predis` (already a dependency) and set `CACHE_STORE`, `QUEUE_CONNECTION`, and/or `SESSION_DRIVER` to `redis` in `.env`.

## Known gaps / not yet built

This is a large spec and most of it is built and tested (see the commit history for a phase-by-phase breakdown), but a few explicitly-requested items are **not** implemented yet:

- PDF/Excel report exports (`barryvdh/laravel-dompdf` and `maatwebsite/excel` are installed but unused)
- Docker Compose setup
- Two-Factor Authentication for Admin (explicitly optional in the spec)

## License

Proprietary — built for simulation/demonstration purposes only.
