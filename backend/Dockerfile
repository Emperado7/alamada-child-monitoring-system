FROM php:8.2-apache

# Install system dependencies + PHP extensions
RUN apt-get update -qq && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Remove any existing .env
RUN rm -f .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Set correct permissions
RUN mkdir -p storage/logs \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

# Configure Apache to serve from Laravel's public/ directory
RUN echo '<VirtualHost *:${PORT:-10000}>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        Options -Indexes\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Tell Apache to listen on $PORT (Render sets this)
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

EXPOSE 10000

# Startup: write .env, run migrations, start Apache
CMD ["sh", "-c", "\
    echo 'APP_NAME=AlamadaCMS' > .env && \
    echo \"APP_ENV=production\" >> .env && \
    echo \"APP_KEY=${APP_KEY}\" >> .env && \
    echo \"APP_DEBUG=false\" >> .env && \
    echo \"APP_URL=${APP_URL}\" >> .env && \
    echo \"DB_CONNECTION=pgsql\" >> .env && \
    echo \"DB_HOST=${DB_HOST}\" >> .env && \
    echo \"DB_PORT=5432\" >> .env && \
    echo \"DB_DATABASE=${DB_DATABASE}\" >> .env && \
    echo \"DB_USERNAME=${DB_USERNAME}\" >> .env && \
    echo \"DB_PASSWORD=${DB_PASSWORD}\" >> .env && \
    echo \"DB_SSLMODE=require\" >> .env && \
    echo \"SESSION_DRIVER=file\" >> .env && \
    echo \"CACHE_DRIVER=file\" >> .env && \
    echo \"QUEUE_CONNECTION=sync\" >> .env && \
    echo \"FILESYSTEM_DISK=public\" >> .env && \
    echo \"SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS}\" >> .env && \
    echo \"SESSION_DOMAIN=.onrender.com\" >> .env && \
    echo \"LOG_CHANNEL=errorlog\" >> .env && \
    echo \"LOG_LEVEL=error\" >> .env && \
    echo '=== .env written ===' && \
    php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    echo '=== Config cached ===' && \
    php artisan migrate --force && \
    echo '=== Migrations done ===' && \
    php artisan db:seed --force --class=DatabaseSeeder && \
    echo '=== Seeding done ===' && \
    php artisan storage:link --force && \
    echo '=== Storage linked ===' && \
    sed -i \"s/Listen 80/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && \
    sed -i \"s/*:80/*:${PORT:-10000}/\" /etc/apache2/sites-available/000-default.conf && \
    echo '=== Starting Apache on port ${PORT:-10000} ===' && \
    exec apache2-foreground"]
