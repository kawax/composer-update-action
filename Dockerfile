FROM php:8.1-cli

# php
RUN apt-get update \
    && apt-get install -yq \
        git \
        libzip-dev \
        zlib1g-dev \
        libgmp-dev \
        unzip \
        libicu-dev\
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) zip pdo_mysql bcmath pcntl gmp intl gd exif

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /root
COPY ./update /root

RUN composer install --no-dev --no-interaction --no-progress --no-scripts

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
