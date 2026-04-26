FROM php:8.4-fpm

ARG PHP_XDEBUG

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    librabbitmq-dev \
    libssh-dev \
    libicu-dev \
    libmagickwand-dev \
    imagemagick

# Очистка кеша
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip sockets opcache intl

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка Xdebug, если PHP_XDEBUG=yes
RUN if [ "$PHP_XDEBUG" = "yes" ]; then \
        pecl install xdebug && docker-php-ext-enable xdebug; \
    else \
        echo "Xdebug disabled"; \
    fi

RUN pecl install imagick && docker-php-ext-enable imagick

# Добавим свой php.ini, можем в нем определять свои значения конфига
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

# Создание пользователя для Laravel
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Копирование существующих прав приложения
COPY --chown=www:www . /var/www/html

# Изменение текущего пользователя на www
USER www

# Открытие порта 9000 и запуск php-fpm сервера
EXPOSE 9000
CMD ["php-fpm"]