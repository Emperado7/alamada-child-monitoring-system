#!/usr/bin/env bash
# Render runtime entrypoint — minimal, no caching, maximum error visibility.
set -o pipefail

cd /var/www/html

APP_PORT="${PORT:-10000}"

echo "=== [1/7] Writing .env ==="
cat > .env <<ENVEOF
APP_NAME="AlamadaCMS"
APP_ENV="production"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="false"
APP_URL="${APP_URL:-https://alamada-child-monitoring-system.onrender.com}"

DB_CONNECTION="${DB_CONNECTION:-pgsql}"
DB_HOST="${DB_HOST:-}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE:-}"
DB_USERNAME="${DB_USERNAME:-}"
DB_PASSWORD="${DB_PASSWORD:-}"
DB_SSLMODE="${DB_SSLMODE:-require}"

SESSION_DRIVER="database"
SESSION_LIFETIME="120"
CACHE_DRIVER="file"
QUEUE_CONNECTION="sync"
FILESYSTEM_DISK="public"

SANCTUM_STATEFUL_DOMAINS="${SANCTUM_STATEFUL_DOMAINS:-alamada-child-monitoring-system.onrender.com,alamada-child-monitoring-system-1.onrender.com}"
SESSION_DOMAIN=".onrender.com"

LOG_CHANNEL="stderr"
LOG_LEVEL="debug"
ENVEOF

echo "=== [2/7] Removing stale bootstrap/cache files ==="
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes*.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php

echo "=== [3/7] Clearing compiled views ==="
rm -rf storage/framework/views/*.php 2>/dev/null || true

echo "=== [4/7] Storage setup ==="
php artisan storage:link --force --no-ansi 2>&1 || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "=== [5/7] Running migrations ==="
php artisan migrate --force --no-ansi 2>&1 || echo "!!! migrate failed — check DB env vars"

echo "=== [6/7] Seeding ==="
php artisan db:seed --force --class=DatabaseSeeder --no-ansi 2>&1 || echo "!!! seed skipped"

echo "=== [7/7] Configuring nginx port ${APP_PORT} ==="
sed -i "s/__PORT__/${APP_PORT}/g" /etc/nginx/http.d/default.conf || echo "!!! sed skipped"

echo "=== Starting supervisord ==="
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
