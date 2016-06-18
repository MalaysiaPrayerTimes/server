FROM php:7.0-fpm

ADD ./laravel.ini /usr/local/etc/php/conf.d

RUN docker-php-ext-install pdo_mysql

RUN usermod -u 1000 www-data

WORKDIR /var/www/mpt

CMD ["php-fpm"]

EXPOSE 9000
