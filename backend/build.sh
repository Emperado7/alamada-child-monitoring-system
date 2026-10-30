#!/usr/bin/env bash
# Render build script for Laravel backend
set -e

echo "=== Installing Composer dependencies ==="
composer install --no-dev --optimize-autoloader

echo "=== Creating storage symlink ==="
php artisan storage:link --force || true

echo "=== Caching config, routes, views ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Seeding database ==="
php artisan db:seed --force --class=DatabaseSeeder

echo "=== Build complete ==="
