FROM php:8.0-apache

RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install mysqli

COPY . /app

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /app && a2enmod rewrite && a2enmod headers
