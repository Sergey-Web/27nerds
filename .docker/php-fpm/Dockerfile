FROM php:8.1-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip netcat libxml2-dev libpq-dev libzip-dev libpng-dev && \
    pecl install apcu xdebug-3.1.5 -o -f redis && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j$(nproc) zip opcache intl gd pdo_pgsql pgsql && \
    docker-php-ext-enable apcu pdo_pgsql sodium gd xdebug redis && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY ./conf.d /usr/local/etc/php/conf.d

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY --chown=www:www . /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

RUN mkdir -p /home/www-data/bash-history/
RUN chown -R www:www /home/www-data/bash-history/

USER www

WORKDIR /var/www/nerds
