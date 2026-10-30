#!/bin/sh
set -e

echo "=== Writing .env ==="
cat > /var/www/html/.env << EOF
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
EOF

cd /var/www/html

echo "=== Clearing and caching config ==="
php artisan config:clear
php artisan config:cache
php artisan route:cache

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Seeding database ==="
php artisan db:seed --force --class=DatabaseSeeder

echo "=== Linking storage ==="
php artisan storage:link --force

echo "=== Configuring Apache port: ${PORT:-10000} ==="
# Update Apache to listen on Render's assigned PORT
sed -i "s/Listen 80/Listen ${PORT:-10000}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT:-10000}>/" /etc/apache2/sites-available/000-default.conf

echo "=== Starting Apache ==="
exec apache2-foreground
