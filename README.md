# OsunBiz

OsunBiz is a Laravel business directory for towns and categories in Osun State. The Laravel application lives at the repository root; the wireframes remain under `docs/wireframes/` as design references.

## Requirements

- PHP 8.3 or newer with the `pdo_pgsql`, `fileinfo`, `mbstring`, and `openssl` extensions
- Composer
- Node.js and npm
- A PostgreSQL database (the project is set up for Neon)

Laravel 13 requires PHP 8.3 or newer.

## First-time setup (PowerShell)

Run these commands from the repository root:

```powershell
$php83 = Join-Path $env:LOCALAPPDATA 'Programs\OsunBiz\php-8.3.36'
if (Test-Path $php83) { $env:PATH = "$php83;$env:PATH" }
php -v
composer install
npm install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
```

In the Neon dashboard, open your project and select **Connect**. Choose the direct connection and copy the host, port, database name, role/user name, and password. Use SSL mode `require`. Add those values to `.env` as `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`. Alternatively, paste the complete PostgreSQL connection string into `DB_URL`; Laravel 13 reads the full connection string from `DB_URL` (not `DATABASE_URL`). Keep `DB_SSLMODE=require` in either case. Never put these credentials in `.env.example` or commit `.env`.

Then run:

```powershell
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Open <http://127.0.0.1:8000>. Keep the terminal running while you use the site; stop the server with `Ctrl+C`.

The local `.env` is intentionally preserved when this repository is updated. If it already exists, do not copy over it; update its values instead.

## Important `.env` settings

| Variable | Purpose | Secret? |
|---|---|---|
| `APP_KEY` | Laravel encryption and signed data | Yes |
| `DB_URL` | Optional full PostgreSQL connection string; Laravel 13 uses this name | Yes |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Individual PostgreSQL connection settings used when `DB_URL` is blank | Password only |
| `DB_SSLMODE` | PostgreSQL SSL mode; use `require` for Neon | No |
| `SESSION_LIFETIME` | Session lifetime in minutes | No |
| `BCRYPT_ROUNDS` | Bcrypt password-hashing work factor | No |
| `FILESYSTEM_DISK` | Active upload disk; keep `local` through Phase 5 | No |
| `AWS_ENDPOINT` | Optional S3-compatible service endpoint | No |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` | S3-compatible storage credentials | Yes |
| `MAIL_PASSWORD` | SMTP authentication password | Yes |

Use `MAIL_SCHEME` for the mail transport scheme; Laravel 13 does not use the older `MAIL_ENCRYPTION` setting.

## Neon values

From the Neon **Connect** panel, use the direct connection details:

| Neon value | `.env` variable |
|---|---|
| Host | `DB_HOST` |
| Port (usually `5432`) | `DB_PORT` |
| Database name | `DB_DATABASE` |
| Role/user | `DB_USERNAME` |
| Password | `DB_PASSWORD` |
| SSL mode | `DB_SSLMODE=require` |
| Optional full connection string | `DB_URL` |

Use either `DB_URL` or the separate `DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` values. If a password contains reserved URL characters, the separate variables avoid URL-encoding mistakes. Do not send the connection string or password in chat.

## Image storage

Image uploads use Laravel's local disk by default (`FILESYSTEM_DISK=local`). A free-tier option to consider for Phase 6 is Cloudflare R2; review its current account limits and pricing before use. The S3 adapter and upload integration are deferred until Phase 6. Once that phase is complete and uploads use Laravel's filesystem abstraction, changing disks requires only `.env` configuration.

To switch the disk without changing application code, set these values in `.env` using the R2 bucket details:

```dotenv
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=your-bucket-name
AWS_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

`AWS_ENDPOINT` is configuration, not a secret. Switching disks does not copy files already stored locally; existing uploads must be copied separately. Keep the disk set to `local` until the storage work in Phase 6.

## Wireframe route map

These are the planned application routes represented by the wireframes; later phases will implement them. `system-states.html` is shared UI behavior rather than a standalone page.

| Wireframe | Planned route or behavior |
|---|---|
| `homepage.html` | `/` |
| `directory.html` | `/directory` |
| `landing.html` | `/directory/{town}/{category}` — town/category landing page |
| `details.html` | `/businesses/{slug}` |
| `reviews.html` | `/businesses/{slug}/reviews` |
| `auth.html` | `/login`, `/register`, `/forgot-password` |
| `reset-password.html` | `/reset-password/{token}` |
| `dashboard.html` | `/dashboard` |
| `owner-dashboard.html` | `/owner/dashboard` |
| `listing-form.html` | `/owner/businesses/create` and `/owner/businesses/{business}/edit` |
| `owner-profile.html` | `/owner/profile` |
| `owner-reviews.html` | `/owner/reviews` — review replies and reporting |
| `admin-review.html` | `/admin/reviews` |
| `admin-manage.html` | `/admin/businesses` |
| `admin-reports.html` | `/admin/reports` |
| `info-pages.html` | `/about`, `/contact`, `/privacy`, `/terms` |
| `system-states.html` | Shared 404 page, flash messages, and email templates |

## Phase 1 foundation

The first phase installs Laravel 13 and Breeze authentication at the repository root, prepares PostgreSQL configuration, and adds models, migrations, and seed data for users, categories, locations, businesses, business images and hours, reviews, and submissions. Business and review field names follow the reviewed schema; business images use `path` and `type`, and reviews include `owner_replied_at`.

The seed command creates the eight starter categories and ten Osun towns. It does not create an administrator account.

## Phase 2 public discovery

The public homepage, searchable directory, town/category pages, business details, and public review pages are backed by the database. Public pages show approved businesses and approved reviews only; wireframe example listings are not presented as live data. Search supports a keyword, town, category, and sort order. Owner submission, review creation, and admin moderation are not part of this phase.

## Phase 3 owner accounts and submissions

New registrations create active owner accounts and require email verification before owner pages are available. Owners can manage their own dashboard and profile, submit business listings, revise pending or rejected submissions, and request moderated changes to their approved listings. Submissions remain private and pending until approved; owners cannot edit another account's records. Optional logo, cover, and gallery uploads are stored on Laravel's local private disk until administrator approval. Administrator moderation is implemented in a later phase.

`Dockerfile` and `render.yaml` are intentionally deferred to Phase 6.
