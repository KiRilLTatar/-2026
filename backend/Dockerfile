FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html
COPY .docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/cyberteka.conf /etc/apache2/conf-available/cyberteka.conf

RUN a2enconf cyberteka

EXPOSE 80
