#!/bin/sh

# ==========================================
# ۱. اجرای Migration ها (ساخت جداول)
# ==========================================
echo "Running migrations..."
php artisan migrate --force

# ==========================================
# ۲. اجرای Seeder (افزودن محصولات نمونه)
# ==========================================
echo "Seeding products..."
php artisan db:seed --class=ProductSeeder --force

# ==========================================
# ۳. اجرای PHP-FPM در پس‌زمینه
# ==========================================
echo "Starting PHP-FPM..."
chmod -R 775 storage bootstrap/cache
php-fpm -D

# ==========================================
# ۴. اجرای Nginx در پیش‌زمینه (کانتینر را زنده نگه می‌دارد)
# ==========================================
echo "Starting Nginx..."
nginx -g 'daemon off;'
