FROM php:7.4-apache-bullseye
LABEL version="1.0.0"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install base PHP extensions.
RUN apt-get update && apt-get install -y \
        libzip-dev \
        unzip \
		git \
        && docker-php-ext-install zip

RUN pecl install phalcon-5.1.0

RUN echo 'extension=phalcon.so' > "$PHP_INI_DIR/conf.d/50-phalcon.ini"

WORKDIR /var/www/
