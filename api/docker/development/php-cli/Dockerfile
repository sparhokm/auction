FROM php:7.4-cli-alpine

RUN apk update && apk add unzip

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d $PHP_INI_DIR/conf.d

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quite

WORKDIR /app
