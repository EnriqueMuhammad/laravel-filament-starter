# syntax=docker/dockerfile:1

FROM php:8.4.27-fpm-bookworm AS base

LABEL org.opencontainers.image.title="Aruna Inventory" \
      org.opencontainers.image.description="Laravel and Filament inventory admin" \
      org.opencontainers.image.licenses="MIT" \
      org.opencontainers.image.source="https://github.com/EnriqueMuhammad/laravel-filament-starter"

WORKDIR /var/www/html

RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        opcache \
        pdo_mysql \
        zip \
    && mkdir -p /usr/local/var/run \
    && chown www-data:www-data /usr/local/var/run

COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/app.ini /usr/local/etc/php/conf.d/app.ini

EXPOSE 9000

FROM base AS development

ARG UID=1000
ARG GID=1000

COPY --from=composer:2.8 --link /usr/bin/composer /usr/bin/composer

RUN groupmod -o -g "${GID}" www-data \
    && usermod -o -u "${UID}" -g "${GID}" www-data

USER www-data

CMD ["php-fpm", "-F"]

FROM base AS build

COPY --from=composer:2.8 --link /usr/bin/composer /usr/bin/composer

COPY --link composer.json composer.lock ./

RUN --mount=type=cache,target=/root/.composer/cache \
    composer install \
        --no-dev \
        --no-interaction \
        --no-scripts \
        --prefer-dist \
        --no-autoloader

COPY --link . .

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --optimize --no-dev --no-scripts \
    && cp .env.example .env \
    && php artisan key:generate --force \
    && php artisan package:discover --ansi \
    && php artisan filament:assets \
    && rm -f .env \
    && chown -R www-data:www-data storage bootstrap/cache public

FROM nginx:1.27.5-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=build /var/www/html/public /var/www/html/public

FROM build AS production

RUN rm -f /usr/bin/composer

USER www-data

CMD ["php-fpm", "-F"]
