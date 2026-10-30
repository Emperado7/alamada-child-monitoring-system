#!/usr/bin/env bash
# Render runtime entrypoint for the Alamada LGU Laravel backend.
# Runs deploy steps (caching, migrations, seeding) then starts nginx + php-fpm via supervisor.
set -u

cd /var/www/html

APP_PORT="${PORT:-10000}"

echo ">>> Writing .env from Render environment variables"
cat > .env <<ENVEOF
APP_NAME="${APP_NAME:-AlamadaCMS}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL}"

DB_CONNECTION="${DB_CONNECTION:-pgsql}"
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"
DB_SSLMODE="${DB_SSLMODE:-require}"

SESSION_DRIVER="${SESSION_DRIVER:-file}"
CACHE_DRIVER="${CACHE_DRIVER:-file}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
FILESYSTEM_DISK="${FILESYSTEM_DISK:-public}"

SANCTUM_STATEFUL_DOMAINS="${SANCTUM_STATEFUL_DOMAINS}"
SESSION_DOMAIN="${SESSION_DOMAIN:-.onrender.com}"

LOG_CHANNEL="${LOG_CHANNEL:-errorlog}"
LOG_LEVEL="${LOG_LEVEL:-error}"
ENVEOF

echo ">>> Caching config, routes, views (runtime, so env vars are present)"
php artisan config:cache --no-ansi 2>&1 || echo "!!! config:cache failed (continuing)"
php artisan route:cache  --no-ansi 2>&1 || echo "!!! route:cache failed (continuing)"
php artisan view:cache   --no-ansi 2>&1 || echo "!!! view:cache failed (continuing)"

echo ">>> Storage symlink + permissions"
php artisan storage:link --force --no-ansi 2>&1 || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Migrations run non-fatally: if the DB is unreachable the web server still binds the
# port, so Render's health check (/api/health) can report DB status instead of the
# container crash-looping with a misleading "port binding" error.
echo ">>> Running migrations"
if ! php artisan migrate --force --no-ansi 2>&1; then
    echo "!!! Migration failed — server will still start. Check /api/health and Render logs."
fi

# Seed is idempotent (firstOrCreate) so it is safe to run on every deploy.
echo ">>> Seeding database"
php artisan db:seed --force --class=DatabaseSeeder --no-ansi 2>&1 || echo "!!! Seed skipped/failed (continuing)"

# Bind nginx to Render's $PORT (default 10000).
echo ">>> Configuring nginx to listen on 0.0.0.0:${APP_PORT}"
sed -i "s/__PORT__/${APP_PORT}/" /etc/nginx/http.d/default.conf

echo ">>> Starting nginx + php-fpm"
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
