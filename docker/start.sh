#!/bin/sh
set -eu

cd /var/www/html

export PORT="${PORT:-10000}"
case "$PORT" in
    ''|*[!0-9]*) printf '%s\n' 'PORT must be a number.' >&2; exit 1 ;;
esac

export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:$PORT}}"
export ASSET_URL="${ASSET_URL:-$APP_URL}"

# Render generates a base64-encoded 256-bit value. Laravel needs its prefix.
APP_KEY="$(php -r '
    $key = getenv("APP_KEY") ?: "";
    if (str_starts_with($key, "base64:")) {
        $decoded = base64_decode(substr($key, 7), true);
    } else {
        $decoded = base64_decode($key, true);
        if ($decoded !== false && strlen($decoded) === 32) {
            $key = "base64:".$key;
        } elseif (strlen($key) === 32) {
            $decoded = $key;
        }
    }
    if ($decoded === false || strlen($decoded) !== 32) {
        fwrite(STDERR, "Set a valid APP_KEY in Render before deploying.\n");
        exit(1);
    }
    echo $key;
')"
export APP_KEY

mkdir -p storage/framework/cache/data storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache
php artisan config:clear

# The free deployment rebuilds only the published invitation, never demo users.
if [ "${DB_CONNECTION:-}" = 'sqlite' ]; then
    if [ "${INVITATION_PUBLIC_ONLY:-}" != 'true' ]; then
        printf '%s\n' 'The bundled SQLite deployment requires INVITATION_PUBLIC_ONLY=true.' >&2
        exit 1
    fi
    mkdir -p "$(dirname "$DB_DATABASE")"
    touch "$DB_DATABASE"
fi

php artisan migrate --force --no-interaction
php artisan db:seed --class=DyAnnDennisSeeder --force --no-interaction

if [ -L public/storage ]; then
    rm public/storage
fi
if [ ! -e public/storage ]; then
    ln -s ../storage/app/public public/storage
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
if [ "${DB_CONNECTION:-}" = 'sqlite' ]; then
    chown www-data:www-data "$(dirname "$DB_DATABASE")" "$DB_DATABASE"
fi

exec apache2-foreground
