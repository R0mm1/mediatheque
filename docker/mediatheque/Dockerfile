FROM php:7.2-fpm

RUN docker-php-ext-install pdo_mysql

COPY init.sh /bin/init.sh

RUN chmod +x /bin/init.sh

CMD init.sh