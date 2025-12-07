FROM php:8.5-fpm

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Konfiguracja i instalacja rozszerzeń PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        opcache

# Instalacja Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instalacja Composera w kontenerze
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instalacja zależności Laravel
COPY . /var/www/html
RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8000