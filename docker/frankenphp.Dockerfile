# Stage 1: Composer dengan PHP 8.2
FROM php:8.2-cli AS vendor

WORKDIR /app

# Install dependency minimal untuk composer
RUN apt-get update && apt-get install -y git unzip libzip-dev libicu-dev \
    && docker-php-ext-install intl zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer files
COPY composer.json composer.lock ./

# Install vendor tanpa artisan scripts
RUN composer install --optimize-autoloader --no-scripts --no-interaction --no-progress


# Stage 2: FrankenPHP (final image, production-ready)
FROM dunglas/frankenphp:1-php8.2

WORKDIR /app

# Install PHP extension yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip gd mbstring exif pcntl bcmath \
    && rm -rf /var/lib/apt/lists/*

# Tambahkan konfigurasi custom & Caddyfile
COPY ./docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./docker/Caddyfile /etc/caddy/Caddyfile

# Copy semua source Laravel
COPY . /app

# Copy vendor dari stage composer
COPY --from=vendor /app/vendor /app/vendor

# Jalankan artisan scripts & cache untuk production
RUN php artisan package:discover --ansi \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

EXPOSE 80
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
