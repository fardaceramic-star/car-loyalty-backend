#!/bin/sh

# تنظیم مجوزها
chmod -R 775 storage bootstrap/cache

# پاک کردن کش‌ها
php artisan config:clear
php artisan route:clear
php artisan view:clear

# انتشار asset ها
php artisan filament:assets

# کش کردن تنظیمات
php artisan config:cache
php artisan route:cache

# اجرای PHP-FPM
php-fpm -D

# اجرای Nginx
nginx -g 'daemon off;'