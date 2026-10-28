#!/bin/sh

# ==========================================
# ۱. تنظیم مجوزها
# ==========================================
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

# ==========================================
# ۲. پاک کردن کش‌های قبلی
# ==========================================
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# ==========================================
# ۳. اجرای Migration ها
# ==========================================
echo "Running migrations..."
php artisan migrate --force

# ==========================================
# ۴. اجرای Seeder
# ==========================================
echo "Seeding products..."
php artisan db:seed --class=ProductSeeder --force

# ==========================================
# ۵. کش کردن تنظیمات (بعد از پاکسازی)
# ==========================================
echo "Caching config and routes..."
php artisan config:cache
php artisan route:cache

# ==========================================
# ۶. اجرای PHP-FPM
# ==========================================
echo "Starting PHP-FPM..."
php-fpm -D

# ==========================================
# ۷. اجرای Nginx
# ==========================================
echo "Starting Nginx..."
nginx -g 'daemon off;'