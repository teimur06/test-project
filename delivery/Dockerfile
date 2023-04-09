# Базовый образ - PHP с FPM
FROM php:8.1-fpm

# Обновляем список пакетов и устанавливаем необходимые пакеты
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        libzip-dev \
        zip \
        unzip \
        git \
        && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        zip \
        opcache

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копируем файлы приложения в контейнер
COPY . /var/www/html

# Устанавливаем права доступа к директории приложения
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Устанавливаем зависимости Composer и генерируем ключ для приложения
RUN composer install --no-interaction --prefer-dist --no-dev && php artisan key:generate

# Экспонируем порт 9000
EXPOSE 9000

# Запускаем PHP-FPM
CMD ["php-fpm"]