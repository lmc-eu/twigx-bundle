FROM php:8.1

# Install unzip utility and libs needed by zip PHP extension
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
# Install and enable extensions
RUN docker-php-ext-install zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# copy files
COPY ./install-composer.sh /tmp

WORKDIR /srv/www/twigx-bundle

RUN bash /tmp/install-composer.sh
