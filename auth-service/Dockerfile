FROM php:7.4

RUN apt-get update && apt-get install -y \
    git \
    iputils-ping

RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . .
RUN composer install
