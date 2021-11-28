FROM php:8-fpm-alpine3.13

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/production/php-fpm/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY ./ ./
