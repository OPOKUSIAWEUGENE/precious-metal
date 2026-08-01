# ---------------------------------------------------------
# Stage 1: Install production Composer dependencies
# ---------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader


# ---------------------------------------------------------
# Stage 2: Laravel runtime with PHP-FPM, Nginx and Supervisor
# ---------------------------------------------------------
FROM php:8.3-fpm

WORKDIR /var/www/html

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        pdo \
        pdo_mysql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Make Composer available in the runtime container
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel application
COPY . .

# Remove locally generated Laravel cache files.
# This prevents stale providers such as Laravel Pail
# from being copied into the production image.
RUN find bootstrap/cache \
    -type f \
    ! -name '.gitignore' \
    -delete

# Copy production dependencies from the Composer stage
COPY --from=vendor /app/vendor ./vendor

# Generate optimized production autoload files
RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative \
    --no-scripts

# Regenerate Laravel's package manifest
RUN php artisan package:discover --ansi

# Copy Nginx, PHP and Supervisor configurations
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/php/php.ini /usr/local/etc/php/conf.d/laravel.ini
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create required Laravel and service directories
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    /run/nginx \
    /var/log/supervisor

# Set Laravel permissions
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    && chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]