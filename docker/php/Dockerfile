FROM php:7.4-fpm-alpine3.12
RUN apk update \
    && apk add --no-cache $PHPIZE_DEPS ca-certificates icu-dev libzip-dev libxml2-dev \
    && apk add --no-cache --virtual .ext-deps openssl-dev \
    && docker-php-ext-install opcache bcmath calendar zip intl xml \
    && apk del .ext-deps \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer; \
cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini;
