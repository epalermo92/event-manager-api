FROM php:7.2-apache

RUN apt-get update \
    && apt-get upgrade -y \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN mkdir -p /var/www/var \
    && chown -R www-data:www-data /var/www

WORKDIR /var/www
