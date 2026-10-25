#!/bin/sh
set -e

printf 'APP_NAME=AlamadaCMS\nAPP_ENV=production\nAPP_KEY=%s\nAPP_DEBUG=false\nAPP_URL=%s\nDB_CONNECTION=pgsql\nDB_HOST=%s\nDB_PORT=5432\nDB_DATABASE=%s\nDB_USERNAME=%s\nDB_PASSWORD=%s\nDB_SSLMODE=require\nSESSION_DRIVER=file\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nFILESYSTEM_DISK=public\nSANCTUM_STATEFUL_DOMAINS=%s\nSESSION_DOMAIN=.onrender.com\nLOG_CHANNEL=errorlog\nLOG_LEVEL=error\n' \
    "$APP_KEY" "$APP_URL" "$DB_HOST" "$DB_DATABASE" "$DB_USERNAME" "$DB_PASSWORD" "$SANCTUM_STATEFUL_DOMAINS" > /app/.env

php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan db:seed --force --class=DatabaseSeeder
php artisan storage:link --force

# Start Apache in foreground
exec /entrypoint supervisord
