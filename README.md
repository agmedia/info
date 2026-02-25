# AG Info

Admin-first content platform (Livewire admin) with desktop/mobile storefront variants.

## Requirements

- PHP `8.4`
- Composer `2.x`
- Node.js `22.x` and npm
- MySQL/MariaDB

## Local Development Setup

1. Install dependencies:
```bash
composer install
npm install
```

2. Create environment file:
```bash
cp .env.example .env
```

3. Configure `.env`:
- `APP_URL` (for Herd/local domain)
- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

4. Generate app key:
```bash
php artisan key:generate
```

5. After `.env` DB credentials are valid and the target database already exists, run migrations:
```bash
php artisan migrate
```

6. Seed data:
```bash
php artisan db:seed
```
Expected prompt:
```text
Seed extended dummy content dataset (users, blog posts, info pages, FAQs)? (yes/no) [no]:
```
- Answer `no` for standard local baseline data.
- Answer `yes` when you need a larger content dataset for performance/testing.
- You can also force this non-interactively with `SEED_DUMMY_DATA=true`.
- To run only dummy content seeding:
```bash
php artisan db:seed --class=Database\\Seeders\\DummyContentSeeder
```

7. Link storage:
```bash
php artisan storage:link
```

8. Build/start frontend assets:
```bash
npm run dev
```

9. Start app/runtime processes (if needed):
```bash
php artisan serve
php artisan queue:work
```

## Default Seeded Users (Non-Superadmin)

These are default local users for quick testing:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@aginfo.local` | `admin` |
| Editor | `editor@aginfo.local` | `editor` |
| Customer | `customer@aginfo.local` | `customer` |

Super-admin users are intentionally not listed in this table.

## API Access Notes

- Wholesale API endpoints are under `/api/v1/wholesale`.
- API access is controlled per user (`api_access_enabled`) and via token abilities.
- CLI token creation:
```bash
php artisan wholesale:token user@example.com client-name
```

## Useful Commands

```bash
php artisan optimize:clear
php artisan test
php artisan route:list
```
