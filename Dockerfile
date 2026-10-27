FROM php:8.2-fpm

# نصب وابستگی‌های سیستمی
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# پاک کردن کش
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# نصب افزونه‌های PHP
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# نصب Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تنظیم پوشه کاری
WORKDIR /var/www/html

# کپی فایل‌های پروژه
COPY . .

# نصب وابستگی‌ها (با کاربر root)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# تنظیم مجوزها
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# کپی فایل‌های کانفیگ Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# کپی فایل‌های Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# اجرای Migration و کش
RUN php artisan config:cache
RUN php artisan route:cache

# پورت
EXPOSE 80

# دستور شروع
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]