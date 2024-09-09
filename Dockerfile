FROM php:8.3

RUN apt-get update && apt-get install --no-install-recommends -y \
    wget \
    vim \
    git \
    unzip \
    libmagickwand-dev \
    libmagickcore-dev \
    libffi-dev

RUN apt-get install --no-install-recommends -y \
    libzip-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    g++ \
    libaio-dev \
    libicu-dev

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && pecl install redis \
    imagick \
    && printf "no\nyes\nno\nyes\n" | pecl install swoole \
    && echo "swoole.use_shortname = Off" >> /usr/local/etc/php/conf.d/swoole.ini \
    && docker-php-ext-install \
    pcntl \
    gd \
    && docker-php-ext-enable \
    swoole \
    imagick

RUN docker-php-ext-configure ffi --with-ffi \
    && docker-php-ext-install ffi

WORKDIR /opt/www

COPY . /opt/www

EXPOSE 9502