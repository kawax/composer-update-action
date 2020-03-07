FROM php:7.4-fpm

ENV DEBIAN_FRONTEND noninteractive

# php
RUN apt-get update \
    && apt-get install -yq git libzip-dev zlib1g-dev libgmp-dev unzip \
    && docker-php-ext-configure zip

RUN docker-php-ext-install zip pdo_mysql bcmath pcntl gmp

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

#RUN composer config -g repos.packagist composer https://packagist.kawax.biz/
RUN composer global require hirak/prestissimo

WORKDIR /root
COPY ./update /root

RUN composer install --no-interaction --prefer-dist --no-progress --no-suggest

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
