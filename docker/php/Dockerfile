FROM php:7.0-fpm

ADD ./laravel.ini /usr/local/etc/php/conf.d

RUN docker-php-ext-install pdo_mysql

RUN curl -s http://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && echo "alias composer='/usr/local/bin/composer.phar'" >> ~/.bashrc

RUN usermod -u 1000 www-data

WORKDIR /var/www/mpt

CMD ["php-fpm"]

EXPOSE 9000
