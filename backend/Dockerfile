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

# Copy everything from the build context (= backend/ on Render)
COPY . .

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

CMD ["sh", "-c", "\
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php artisan db:seed --force --class=DatabaseSeeder && \
    php artisan storage:link --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
