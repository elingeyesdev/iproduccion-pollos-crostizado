
# Etapa 1: compilar los archivos de Vite
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# Etapa 2: ejecutar Laravel con Apache
FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libonig-dev libxml2-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && a2enmod rewrite headers \
    && sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY --from=frontend /app/public/build ./public/build

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan storage:link --force \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000

CMD ["apache2-foreground"]
