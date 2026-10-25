FROM php:8.2-cli-alpine

# Alpine package names differ from Debian — use correct Alpine equivalents
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        zip \
        bcmath \
        xml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Remove any local .env — Render injects env vars at runtime
RUN rm -f .env

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p storage/logs \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD ["sh", "-c", "\
    printf 'APP_NAME=AlamadaCMS\nAPP_ENV=production\nAPP_KEY=%s\nAPP_DEBUG=false\nAPP_URL=%s\nDB_CONNECTION=pgsql\nDB_HOST=%s\nDB_PORT=5432\nDB_DATABASE=%s\nDB_USERNAME=%s\nDB_PASSWORD=%s\nDB_SSLMODE=require\nSESSION_DRIVER=file\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nFILESYSTEM_DISK=public\nSANCTUM_STATEFUL_DOMAINS=%s\nSESSION_DOMAIN=.onrender.com\nLOG_CHANNEL=stack\nLOG_LEVEL=error\n' \
        \"$APP_KEY\" \"$APP_URL\" \"$DB_HOST\" \"$DB_DATABASE\" \"$DB_USERNAME\" \"$DB_PASSWORD\" \"$SANCTUM_STATEFUL_DOMAINS\" > .env \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan migrate --force \
    && php artisan db:seed --force --class=DatabaseSeeder \
    && php artisan storage:link --force \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
