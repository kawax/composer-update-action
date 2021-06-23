FROM php:7.4-cli

# php
RUN apt-get update
RUN apt-get install -yq git libzip-dev unzip
RUN docker-php-ext-install zip
RUN docker-php-ext-install -j$(nproc) zip

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /root
COPY ./update /root

RUN composer install --no-dev --no-interaction --no-progress --no-scripts

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
