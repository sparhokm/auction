FROM php:8.3-cli-alpine as builder

RUN apk update && apk add unzip

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quite

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-scripts --optimize-autoloader \
    && rm -rf /root/.composer/cache

### FPM ###

FROM php:8.3-fpm-alpine

RUN docker-php-ext-install opcache

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/common/php/conf.d $PHP_INI_DIR/conf.d
COPY ./docker/production/php/conf.d $PHP_INI_DIR/conf.d

WORKDIR /app

COPY --from=builder /app ./

COPY ./ ./

RUN chown www-data:www-data ./var -R
