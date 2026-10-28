#!/bin/sh

# ==========================================
# ۱. تنظیم مجوزهای پوشه‌های storage و bootstrap/cache
# ==========================================
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

# ==========================================
# ۲. پاک کردن کش‌های قبلی (برای جلوگیری از خطاهای ناشی از کش)
# ==========================================
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# ==========================================
# ۳. اجرای Migration ها (ساخت جداول)
# ==========================================
echo "Running migrations..."
php artisan migrate --force

# ==========================================
# ۴. اجرای Seeder (افزودن محصولات نمونه)
# ==========================================
echo "Seeding products..."
php artisan db:seed --class=ProductSeeder --force

# ==========================================
# ۵. بررسی فعال بودن افزونه intl (برای دیباگ)
# ==========================================
echo "Checking intl extension..."
php -m | grep intl || echo "WARNING: intl extension is not enabled!"

# ==========================================
# ۶. اجرای PHP-FPM در پس‌زمینه
# ==========================================
echo "Starting PHP-FPM..."
php-fpm -D

# ==========================================
# ۷. اجرای Nginx در پیش‌زمینه (کانتینر را زنده نگه می‌دارد)
# ==========================================
echo "Starting Nginx..."
nginx -g 'daemon off;'
