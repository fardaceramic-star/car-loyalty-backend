FROM php:8.2-fpm

# ==========================================
# ۱. نصب وابستگی‌های سیستمی
# ==========================================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ==========================================
# ۲. نصب افزونه‌های PHP
# ==========================================
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# ==========================================
# ۳. تنظیم محدودیت‌های آپلود (PHP)
# ==========================================
RUN echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# ==========================================
# ۴. نصب Composer
# ==========================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ==========================================
# ۵. تنظیم پوشه کاری
# ==========================================
WORKDIR /var/www/html

# ==========================================
# ۶. کپی فایل‌های پروژه
# ==========================================
COPY . .

# ==========================================
# ۷. نصب وابستگی‌های Laravel
# ==========================================
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# ==========================================
# ۸. تنظیم مجوزها
# ==========================================
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ==========================================
# ۹. کپی فایل‌های کانفیگ Nginx
# ==========================================
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# ==========================================
# ۱۰. کش کردن تنظیمات لاراول
# ==========================================
RUN php artisan config:cache
RUN php artisan route:cache

# ==========================================
# ۱۱. پورت و دستور شروع
# ==========================================
EXPOSE 80

CMD ["sh", "/var/www/html/start.sh"]