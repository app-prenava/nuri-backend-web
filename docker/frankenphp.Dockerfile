FROM php:8.2-cli AS vendor

WORKDIR /app

RUN apt-get update && apt-get install -y git unzip libzip-dev libicu-dev \
    && docker-php-ext-install intl zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --optimize-autoloader --no-scripts --no-interaction --no-progress


FROM dunglas/frankenphp:1-php8.2

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip gd mbstring exif pcntl bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY ./docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./docker/Caddyfile /etc/caddy/Caddyfile

COPY . /app

COPY --from=vendor /app/vendor /app/vendor

RUN php artisan package:discover --ansi \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

EXPOSE 80
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
