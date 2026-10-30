#!/bin/sh
set -e

# Use Render's PORT or default 10000
APP_PORT="${PORT:-10000}"

echo ">>> Writing .env"
cat > /app/.env << ENVEOF
APP_NAME=AlamadaCMS
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=${APP_URL}
DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=5432
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DB_SSLMODE=require
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS}
SESSION_DOMAIN=.onrender.com
LOG_CHANNEL=errorlog
LOG_LEVEL=error
ENVEOF

echo ">>> Config cache"
php artisan config:clear --no-ansi 2>&1 || true
php artisan config:cache --no-ansi 2>&1 || true
php artisan route:cache  --no-ansi 2>&1 || true

echo ">>> Migrate"
php artisan migrate --force --no-ansi 2>&1

echo ">>> Seed"
php artisan db:seed --force --class=DatabaseSeeder --no-ansi 2>&1 || true

echo ">>> Storage link"
php artisan storage:link --force --no-ansi 2>&1 || true

echo ">>> Starting PHP server on 0.0.0.0:${APP_PORT}"
exec php -S 0.0.0.0:${APP_PORT} -t /app/public /app/public/index.php
