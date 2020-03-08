FROM php:cli

ENV DEBIAN_FRONTEND noninteractive

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
    && docker-php-ext-install -j$(nproc) zip pdo_mysql bcmath pcntl gmp intl gd

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

#RUN composer config -g repos.packagist composer https://packagist.kawax.biz/
RUN composer global require hirak/prestissimo

WORKDIR /root
COPY ./update /root

RUN composer install  --no-dev --no-interaction --prefer-dist --no-progress --no-suggest --optimize-autoloader

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
