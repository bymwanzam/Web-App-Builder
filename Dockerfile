# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1: build front-end assets (Vite + Tailwind)
# ---------------------------------------------------------------------------
FROM node:22-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js ./
COPY resources ./resources
COPY app/Plugins ./app/Plugins
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2: PHP + Apache runtime
# ---------------------------------------------------------------------------
FROM php:8.3-apache AS app

# PHP extensions required by the installer's requirement check and dependencies
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        bcmath exif gd imagick intl opcache pcntl pdo_mysql pdo_sqlite zip \
    && apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Apache: serve from /public, allow .htaccess rewrites
RUN a2enmod rewrite headers \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-appy.ini

WORKDIR /var/www/html

# Install PHP dependencies first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

# Application source
COPY . .
COPY --from=assets /app/public/build ./public/build

# The base image makes /var/www/html world-writable + sticky (1777); with the
# kernel's protected_symlinks rule that stops www-data from following the
# root-created .env symlink, so reset it to a normal directory mode.
RUN chmod 0755 /var/www/html \
    && composer dump-autoload --optimize --no-dev \
    && rm -rf public/storage \
    && ln -s ../storage/app/public public/storage \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
                storage/framework/views storage/logs bootstrap/cache docker-data \
    && chown -R www-data:www-data storage bootstrap/cache database docker-data public

COPY docker/entrypoint.sh /usr/local/bin/appy-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/appy-entrypoint && chmod +x /usr/local/bin/appy-entrypoint

VOLUME ["/var/www/html/storage", "/var/www/html/docker-data"]
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -fsS http://localhost/up > /dev/null || exit 1

ENTRYPOINT ["appy-entrypoint"]
CMD ["apache2-foreground"]
