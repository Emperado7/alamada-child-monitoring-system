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

# Set storage / bootstrap permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
             storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render injects $PORT at runtime — expose the default as a hint
EXPOSE 10000

# start.sh is the entrypoint — see below
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
