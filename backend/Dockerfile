# Render deployment image for the Alamada LGU Laravel backend.
# Follows Render's official PHP/Laravel Docker guide:
# https://render.com/docs/deploy-php-laravel-docker
#
# Stack: PHP-FPM (fastcgi) + Nginx (static + reverse proxy) + Supervisor (process manager).
# nginx is templated at runtime to listen on Render's $PORT (default 10000) so the
# service binds to 0.0.0.0:$PORT as Render requires.

FROM php:8.2-fpm-alpine

# ── System packages + PHP extensions ──────────────────────────────────────────
# Runtime: nginx, supervisor, libpq (pgsql), libzip, icu-libs.
# Build deps (postgresql-dev, libzip-dev, icu-dev) are removed afterwards.
RUN apk add --no-cache bash nginx supervisor libpq libzip icu-libs curl \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS postgresql-dev libzip-dev icu-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql intl zip opcache bcmath \
    && apk del .build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── PHP / PHP-FPM / Nginx / Supervisor configuration ─────────────────────────────
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
COPY conf/php/docker-vars.ini   /usr/local/etc/php/conf.d/docker-vars.ini
COPY conf/php/www-overrides.conf /usr/local/etc/php-fpm.d/zz-overrides.conf
COPY conf/nginx/nginx-site.conf /etc/nginx/http.d/default.conf
COPY conf/supervisor/supervisord.conf /etc/supervisord.conf
COPY start.sh /start.sh
RUN chmod +x /start.sh

# ── Application ────────────────────────────────────────────────────────────────
WORKDIR /var/www/html
COPY . .
RUN rm -f .env

# Install dependencies at build time (no runtime env vars required for composer).
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Writable storage / cache for the web server user.
RUN mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions \
             storage/framework/views storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ── Defaults (overridable by Render env vars) ───────────────────────────────────
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Render injects PORT (default 10000). nginx is bound to it at runtime by start.sh.
EXPOSE 10000

CMD ["/start.sh"]
