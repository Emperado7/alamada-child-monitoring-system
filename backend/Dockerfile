FROM php:8.2-cli

# Install system deps + PostgreSQL libs
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project (excluding .env via .dockerignore)
COPY . .

# Remove any local .env that may have been copied — Render injects env vars at runtime
RUN rm -f .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions
RUN mkdir -p storage/logs \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 10000

# At runtime: create .env from Render's injected env vars, then migrate + serve
CMD ["sh", "-c", "\
    echo 'APP_NAME=\"Alamada LGU Child Monitoring System\"' > .env && \
    echo \"APP_ENV=${APP_ENV:-production}\" >> .env && \
    echo \"APP_KEY=${APP_KEY}\" >> .env && \
    echo \"APP_DEBUG=${APP_DEBUG:-false}\" >> .env && \
    echo \"APP_URL=${APP_URL}\" >> .env && \
    echo \"DB_CONNECTION=${DB_CONNECTION:-pgsql}\" >> .env && \
    echo \"DB_HOST=${DB_HOST}\" >> .env && \
    echo \"DB_PORT=${DB_PORT:-5432}\" >> .env && \
    echo \"DB_DATABASE=${DB_DATABASE:-postgres}\" >> .env && \
    echo \"DB_USERNAME=${DB_USERNAME}\" >> .env && \
    echo \"DB_PASSWORD=${DB_PASSWORD}\" >> .env && \
    echo \"DB_SSLMODE=${DB_SSLMODE:-require}\" >> .env && \
    echo \"SESSION_DRIVER=${SESSION_DRIVER:-file}\" >> .env && \
    echo \"SESSION_LIFETIME=120\" >> .env && \
    echo \"CACHE_DRIVER=file\" >> .env && \
    echo \"QUEUE_CONNECTION=sync\" >> .env && \
    echo \"FILESYSTEM_DISK=public\" >> .env && \
    echo \"SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS}\" >> .env && \
    echo \"SESSION_DOMAIN=${SESSION_DOMAIN:-.onrender.com}\" >> .env && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php artisan db:seed --force --class=DatabaseSeeder && \
    php artisan storage:link --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
