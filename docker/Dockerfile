FROM php:8.1

# Install unzip utility and libs needed by zip PHP extension
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip

# copy files
COPY ./install-composer.sh /tmp

WORKDIR /srv/www/twigx-bundle

RUN bash /tmp/install-composer.sh


