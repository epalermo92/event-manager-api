FROM php:7.3-apache
COPY . /var/www/event-manager-app
WORKDIR /var/www/event-manager-app

RUN apt-get update
RUN apt-get upgrade -y
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite