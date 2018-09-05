FROM php:7-apache
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_NO_INTERACTION 1
RUN apt update
RUN apt install -y git zip
RUN docker-php-ext-install mysqli
RUN curl -sS https://getcomposer.org/installer | php
RUN php -d memory_limit=-1 composer.phar require aws/aws-sdk-php
COPY ./html /var/www/html
