FROM talentify/phalcon-framework:4.1-php-7.4-apache-bullseye

WORKDIR /var/www/

# Install git in order to allow composer to clone talentify/ repositories.
RUN set -eux; \
	apt-get update; \
	apt-get install -y \
	git \
	unzip \
	&& rm -rf /var/lib/apt/lists/*

# Add composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
