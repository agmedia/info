#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    case "${APP_ENV:-production}" in
        local|development|dev)
            if [ -f .env.example ]; then
                cp .env.example .env
            elif [ -f .env.docker.example ]; then
                cp .env.docker.example .env
            fi
            ;;
    esac
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

if [ -z "${APP_KEY:-}" ]; then
    unset APP_KEY
fi

env_file_value() {
    [ -f .env ] || return 0

    php -r '
        require "vendor/autoload.php";
        $values = Dotenv\Dotenv::parse(file_get_contents(".env"));
        echo (string) ($values[$argv[1]] ?? "");
    ' "$1"
}

file_app_env="$(env_file_value APP_ENV)"
file_app_key="$(env_file_value APP_KEY)"

if [ "${APP_ENV+x}" = x ]; then
    configured_app_env="${APP_ENV:-production}"
else
    configured_app_env="${file_app_env:-production}"
fi

configured_app_key="${APP_KEY:-$file_app_key}"

if [ -z "$configured_app_key" ]; then
    case "$configured_app_env" in
        local|development|dev)
            if [ ! -f .env ]; then
                echo 'APP_KEY is missing and no .env file is available for local initialization.' >&2
                exit 1
            fi

            php artisan config:clear >/dev/null
            php artisan key:generate --force >/dev/null

            if [ -z "$(env_file_value APP_KEY)" ]; then
                echo 'Failed to persist the generated APP_KEY.' >&2
                exit 1
            fi
            ;;
        *)
            printf 'APP_KEY must be configured persistently for APP_ENV=%s; refusing to generate one at startup.\n' \
                "$configured_app_env" >&2
            exit 1
            ;;
    esac
fi

php artisan migrate --force >/dev/null 2>&1 || true

exec "$@"
