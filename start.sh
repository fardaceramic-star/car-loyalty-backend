#!/bin/sh

# ==========================================
# ۱. تنظیم مجوزها
# ==========================================
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

# ==========================================
# ۲. پاک کردن کش‌ها
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
# ۵. ساخت کاربر ادمین
# ==========================================
echo "Creating admin user (if not exists)..."
php artisan tinker --execute="\App\Models\User::firstOrCreate(['email' => 'admin@yahoo.com'], ['name' => 'مدیر', 'password' => bcrypt('Aa123456')]);"

# ==========================================
# ۶. انتشار فایل‌های استاتیک Filament
# ==========================================
echo "Publishing Filament assets..."
php artisan filament:assets

# ==========================================
# ۷. کش کردن تنظیمات
# ==========================================
echo "Caching config and routes..."
php artisan config:cache
php artisan route:cache

# ==========================================
# ۸. اجرای PHP-FPM روی Unix Socket (نه TCP)
# ==========================================
echo "Starting PHP-FPM..."
php-fpm -D

# ==========================================
# ۹. اجرای Nginx
# ==========================================
echo "Starting Nginx..."
nginx -g 'daemon off;'