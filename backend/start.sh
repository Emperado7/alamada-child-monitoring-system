#!/bin/sh
set -e

printf 'APP_NAME=AlamadaCMS\nAPP_ENV=production\nAPP_KEY=%s\nAPP_DEBUG=false\nAPP_URL=%s\nDB_CONNECTION=pgsql\nDB_HOST=%s\nDB_PORT=5432\nDB_DATABASE=%s\nDB_USERNAME=%s\nDB_PASSWORD=%s\nDB_SSLMODE=require\nSESSION_DRIVER=file\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nFILESYSTEM_DISK=public\nSANCTUM_STATEFUL_DOMAINS=%s\nSESSION_DOMAIN=.onrender.com\nLOG_CHANNEL=errorlog\nLOG_LEVEL=error\n' \
    "$APP_KEY" "$APP_URL" "$DB_HOST" "$DB_DATABASE" "$DB_USERNAME" "$DB_PASSWORD" "$SANCTUM_STATEFUL_DOMAINS" > /app/.env

php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan db:seed --force --class=DatabaseSeeder
php artisan storage:link --force

# Run the Laravel scheduler every minute in the background.
# The KeepAlive command (registered at every 14 minutes) will fire
# automatically from within this loop to prevent Render cold starts.
(while true; do php artisan schedule:run >> /dev/null 2>&1; sleep 60; done) &

# Start Apache in foreground
exec /entrypoint supervisord
