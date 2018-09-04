FROM php:7-apache
RUN docker-php-ext-install mysqli
COPY ./html /var/www/html
