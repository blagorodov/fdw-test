FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    git \
    unzip \
    curl \
    xz \
    && docker-php-ext-install pdo_pgsql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer \
    && composer --version
