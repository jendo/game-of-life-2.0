FROM php:7.4.27-fpm

ARG APP_WORK_DIR=/project
ARG COMPOSER_VERSION=1.10.10
ARG COMPOSER_SHA256=8f16aa77b1236ed40855d8d141c0a939d108a939c8e73f9f32eadd3f05f181b9

RUN \
 usermod -u 1000 www-data && \
 groupmod -g 1000 www-data && \
 usermod -d /home/www-data -m -s /bin/bash www-data && \
 chown -R www-data:www-data /home/www-data

RUN \
 apt-get update && \
 apt-get install -y vim git curl zip unzip

RUN curl -Ls "https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar" > /usr/local/bin/composer && \
    test "$(sha256sum /usr/local/bin/composer | cut -d ' ' -f 1)" = "${COMPOSER_SHA256}" && \
    chmod +x /usr/local/bin/composer && \
    chown www-data:www-data /usr/local/bin/composer

# xdebug
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

# PDO MYSQL
RUN docker-php-ext-install pdo_mysql

# Setup environment
ENV DEV=1
RUN mkdir -p $APP_WORK_DIR
RUN chmod -R 755 $APP_WORK_DIR
RUN chown -R www-data:www-data $APP_WORK_DIR

WORKDIR $APP_WORK_DIR
