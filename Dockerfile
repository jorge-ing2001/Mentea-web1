# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1: build frontend assets with Vite (Node 20)
# The app references assets via @vite(...) in Blade, so production needs the
# compiled manifest + files in public/build or the UI breaks.
# ---------------------------------------------------------------------------
FROM node:20-alpine AS assets

WORKDIR /app

# Install JS deps first (better layer caching).
COPY package.json package-lock.json ./
RUN npm ci

# Copy only what Vite needs to build.
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2: PHP runtime (php:8.3-apache)
# ---------------------------------------------------------------------------
FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Bring in the Vite build produced in the assets stage.
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && php artisan storage:link || true \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN a2enmod rewrite \
    && printf '%s\n' \
    '<VirtualHost *:80>' \
    '    DocumentRoot /var/www/html/public' \
    '    <Directory /var/www/html/public>' \
    '        AllowOverride All' \
    '        Require all granted' \
    '    </Directory>' \
    '    ErrorLog ${APACHE_LOG_DIR}/error.log' \
    '    CustomLog ${APACHE_LOG_DIR}/access.log combined' \
    '</VirtualHost>' \
    > /etc/apache2/sites-available/000-default.conf \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

EXPOSE 80

# Cache only at container START, after Render injects env (APP_KEY, DATABASE_URL,
# etc.). Caching at build time would fail/poison caches because env is absent.
# config:cache + route:cache need a valid env, so they run here, not at build.
CMD ["sh", "-c", "php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground"]
