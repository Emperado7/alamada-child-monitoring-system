FROM php:8.2-cli

# Install dependencies
RUN apt-get update -qq \
    && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN rm -f .env

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

RUN mkdir -p storage/logs \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 10000

# Use a startup script for cleaner error handling
CMD ["sh", "-c", "\
    printf 'APP_NAME=AlamadaCMS\nAPP_ENV=production\nAPP_KEY=%s\nAPP_DEBUG=false\nAPP_URL=%s\nDB_CONNECTION=pgsql\nDB_HOST=%s\nDB_PORT=5432\nDB_DATABASE=%s\nDB_USERNAME=%s\nDB_PASSWORD=%s\nDB_SSLMODE=require\nSESSION_DRIVER=file\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nFILESYSTEM_DISK=public\nSANCTUM_STATEFUL_DOMAINS=%s\nSESSION_DOMAIN=.onrender.com\nLOG_CHANNEL=errorlog\nLOG_LEVEL=error\n' \
    \"$APP_KEY\" \"$APP_URL\" \"$DB_HOST\" \"$DB_DATABASE\" \"$DB_USERNAME\" \"$DB_PASSWORD\" \"$SANCTUM_STATEFUL_DOMAINS\" > .env \
    && echo '=== .env written ===' \
    && php artisan config:clear \
    && php artisan config:cache \
    && php artisan route:cache \
    && echo '=== Config cached ===' \
    && php artisan migrate --force \
    && echo '=== Migrations done ===' \
    && php artisan db:seed --force --class=DatabaseSeeder \
    && echo '=== Seeding done ===' \
    && php artisan storage:link --force \
    && echo '=== Starting server on port ${PORT:-10000} ===' \
    && exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
