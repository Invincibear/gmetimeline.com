FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock* /var/www/

COPY . /var/www/gmetimeline/

WORKDIR /var/www/gmetimeline
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/gmetimeline

ENV APACHE_DOCUMENT_ROOT="/var/www/gmetimeline/httpdocs"
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo "ServerName gmetimeline.com" >> /etc/apache2/apache2.conf


EXPOSE 80
