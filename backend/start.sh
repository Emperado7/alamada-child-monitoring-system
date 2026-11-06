#!/usr/bin/env bash
# Render runtime entrypoint
# APP_ENV=production, APP_DEBUG=false for live use.
# Session uses database driver to avoid file permission issues on Render.

cd /var/www/html

APP_PORT="${PORT:-10000}"

echo ">>> Writing .env"
cat > .env <<ENVEOF
APP_NAME="${APP_NAME:-AlamadaCMS}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
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
CACHE_DRIVER="${CACHE_DRIVER:-file}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
FILESYSTEM_DISK="${FILESYSTEM_DISK:-public}"

SANCTUM_STATEFUL_DOMAINS="${SANCTUM_STATEFUL_DOMAINS:-alamada-child-monitoring-system.onrender.com,alamada-child-monitoring-system-1.onrender.com}"
SESSION_DOMAIN="${SESSION_DOMAIN:-.onrender.com}"

LOG_CHANNEL="${LOG_CHANNEL:-errorlog}"
LOG_LEVEL="${LOG_LEVEL:-error}"
ENVEOF

echo ">>> Clearing stale caches"
php artisan config:clear --no-ansi 2>&1 || true
php artisan route:clear  --no-ansi 2>&1 || true
php artisan view:clear   --no-ansi 2>&1 || true

echo ">>> Caching config + routes + views"
php artisan config:cache --no-ansi 2>&1 || echo "!!! config:cache skipped"
php artisan route:cache  --no-ansi 2>&1 || echo "!!! route:cache skipped"
php artisan view:cache   --no-ansi 2>&1 || echo "!!! view:cache skipped"

echo ">>> Storage symlink + permissions"
php artisan storage:link --force --no-ansi 2>&1 || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ">>> Running migrations (includes sessions table)"
php artisan migrate --force --no-ansi 2>&1 || echo "!!! migrate skipped"

echo ">>> Seeding"
php artisan db:seed --force --class=DatabaseSeeder --no-ansi 2>&1 || echo "!!! seed skipped"

echo ">>> Configuring nginx port to ${APP_PORT}"
sed -i "s/__PORT__/${APP_PORT}/g" /etc/nginx/http.d/default.conf || echo "!!! sed skipped"

echo ">>> Starting supervisord"
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
