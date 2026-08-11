# syntax=docker/dockerfile:1

############################################
# Stage 1 - PHP dependencies (Composer)
############################################
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs \
    --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-dev

############################################
# Stage 2 - Runtime image (PHP-FPM)
############################################
FROM php:8.2-fpm-alpine AS app

RUN apk add --no-cache \
        bash \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        libxml2-dev \
        oniguruma-dev \
        icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        dom \
        intl \
        opcache

# Composer binary (handy for `docker compose exec app composer ...`)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# App source (see .dockerignore for what's excluded, notably .env, vendor, node_modules)
COPY . .

# Pre-built dependencies from the earlier stage
COPY --from=vendor /app/vendor ./vendor

# PHP tuning (file upload size etc. — SIMA stores PDF/image attachments up to 4MB)
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Entrypoint: waits for DB, runs migrations, links storage, etc.
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
