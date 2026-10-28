FROM php:8.2-fpm

# نصب وابستگی‌ها (شامل libicu-dev و libpq-dev)
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx \
    libpq-dev libzip-dev libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# نصب افزونه‌های PHP
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# نصب Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# تنظیم مجوزها
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# کپی کانفیگ Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# اجرای اسکریپت شروع
CMD ["sh", "/var/www/html/start.sh"]

