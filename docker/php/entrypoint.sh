#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache || true

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ -f package.json ]; then
    need_front_build=0
    lock_hash=""

    if [ -f package-lock.json ]; then
        lock_hash="$(sha256sum package-lock.json | awk '{print $1}')"
    fi

    current_hash=""
    if [ -f node_modules/.package-lock.hash ]; then
        current_hash="$(cat node_modules/.package-lock.hash)"
    fi

    if [ ! -d node_modules ] || [ "$lock_hash" != "$current_hash" ]; then
        npm ci --no-audit --no-fund
        mkdir -p node_modules
        printf '%s' "$lock_hash" > node_modules/.package-lock.hash
        need_front_build=1
    fi

    if [ ! -f public/build/manifest.json ] \
        || [ resources/css/app.css -nt public/build/manifest.json ] \
        || [ resources/js/app.js -nt public/build/manifest.json ] \
        || [ vite.config.js -nt public/build/manifest.json ] \
        || [ package-lock.json -nt public/build/manifest.json ]; then
        need_front_build=1
    fi

    if [ "$need_front_build" -eq 1 ]; then
        npm run build
    fi
fi

php artisan key:generate --force >/dev/null 2>&1 || true
php artisan migrate --force >/dev/null 2>&1 || true

exec "$@"
