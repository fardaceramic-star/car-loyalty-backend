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
    supervisor \
    libpq-dev \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ==========================================
# ۲. نصب افزونه‌های PHP
# ==========================================
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl
# ==========================================
# ۳. نصب Composer
# ==========================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ==========================================
# ۴. تنظیم پوشه کاری
# ==========================================
WORKDIR /var/www/html

# ==========================================
# ۵. کپی فایل‌های پروژه
# ==========================================
COPY . .

# ==========================================
# ۶. نصب وابستگی‌های Laravel
# ==========================================
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader -vvv

# ==========================================
# ۷. تنظیم مجوزها
# ==========================================
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ==========================================
# ۸. کپی فایل‌های کانفیگ Nginx و Supervisor
# ==========================================
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ==========================================
# ۹. بهینه‌سازی Laravel (اختیاری)
# ==========================================
RUN php artisan config:cache
RUN php artisan route:cache

# ==========================================
# ۱۰. پورت و دستور شروع
# ==========================================
EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]