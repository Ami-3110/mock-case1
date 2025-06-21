FROM php:8.2.20-fpm-alpine

RUN apk add --no-cache \
    bash \
    curl \
    git \
    icu-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    zlib-dev \
    unzip \
    mysql-client \
    nodejs \
    npm \
  && docker-php-ext-install \
    intl \
    pdo_mysql \
    zip \
    gd \
  && curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && rm -rf /var/cache/apk/* /tmp/*

COPY ./docker/php/php.ini /usr/local/etc/php/

WORKDIR /var/www/
