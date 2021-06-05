FROM php:7.4-cli

# php
RUN apt-get install -yq git

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /root
COPY ./update /root

RUN composer install --no-dev --no-interaction --no-progress

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
