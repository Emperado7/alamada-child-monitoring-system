FROM php:8.2-cli

# Install system dependencies + PHP extensions
RUN apt-get update -qq && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application
COPY . .

# Remove stale .env
RUN rm -f .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Ensure writable directories
RUN mkdir -p storage/logs \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 10000

CMD ["/bin/sh", "/app/start.sh"]
