FROM php:7.2-fpm

RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu-5.1.8
RUN docker-php-ext-enable apcu

WORKDIR /usr/src/app

RUN chown -R www-data:www-data ./

RUN PATH=$PATH:/usr/src/apps/vendor/bin:bin