FROM php:8.2-cli

# Install system dependencies + PostgreSQL client libraries
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies (production, optimised autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set storage / cache permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose Railway's expected port
EXPOSE 10000

# Run migrations + seed then start the built-in server
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan storage:link --force && \
    php artisan serve --host=0.0.0.0 --port=10000
