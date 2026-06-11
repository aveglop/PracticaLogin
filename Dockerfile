FROM php:8.4-apache


RUN docker-php-ext-install pdo pdo_mysql

RUN mv "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini" 