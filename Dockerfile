FROM php:8.3-apache-bookworm AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libicu-dev libzip-dev libpng-dev libjpeg62-turbo-dev \
        libfreetype6-dev libonig-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath gd intl mbstring opcache pdo_mysql pdo_sqlite zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

FROM php-base AS dependencies
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev --no-scripts --no-interaction --no-progress --prefer-dist

FROM node:22-bookworm-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public/assets ./public/assets
COPY --from=dependencies /var/www/html/vendor/laravel ./vendor/laravel
RUN npm run build

FROM php-base AS runtime
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_TIMEZONE=Asia/Manila \
    INVITATION_PUBLIC_ONLY=true \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/invitation.sqlite \
    SESSION_DRIVER=file \
    SESSION_SECURE_COOKIE=true \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=warning \
    FILESYSTEM_DISK=public \
    PORT=10000

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY . .
COPY --from=dependencies /var/www/html/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p storage/framework/cache/data storage/framework/sessions \
        storage/framework/views storage/logs bootstrap/cache \
    && COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --no-dev --optimize --no-interaction \
    && cp docker/apache.conf /etc/apache2/sites-available/000-default.conf \
    && printf 'Listen ${PORT}\n' > /etc/apache2/ports.conf \
    && printf 'ServerName localhost\nServerTokens Prod\nServerSignature Off\n' \
        > /etc/apache2/conf-available/invitation.conf \
    && a2enconf invitation \
    && chmod +x docker/start.sh

EXPOSE 10000
CMD ["/var/www/html/docker/start.sh"]
