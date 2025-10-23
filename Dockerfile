FROM php:8.2-fpm

# Instalacja rozszerzeń PHP potrzebnych Laravelowi
RUN docker-php-ext-install pdo pdo_mysql

# Instalacja Composera w kontenerze
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instalacja zależności Laravel
COPY . /var/www/html
RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8000
