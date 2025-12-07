FROM php:8.1-apache

# Устанавливаем необходимые расширения PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip

# Включаем модуль mod_rewrite
RUN a2enmod rewrite

# Устанавливаем timezone
RUN echo "date.timezone = Europe/Moscow" > /usr/local/etc/php/conf.d/timezone.ini

# Создаем папку images для загрузки аватаров
RUN mkdir -p /var/www/html/images && chmod 777 /var/www/html/images

# Копируем все файлы проекта
COPY . /var/www/html/

# Копируем конфигурацию Apache из папки docker (не php!)
COPY ./docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Устанавливаем правильные права
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod 777 /var/www/html/images

WORKDIR /var/www/html