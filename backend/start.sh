#!/bin/sh
set -e

# Use Render's injected PORT, default to 10000
PORT="${PORT:-10000}"

echo "==> Caching config & routes..."
php artisan config:cache
php artisan route:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding database..."
php artisan db:seed --force --class=DatabaseSeeder

echo "==> Linking storage..."
php artisan storage:link --force

echo "==> Starting server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port="$PORT"
