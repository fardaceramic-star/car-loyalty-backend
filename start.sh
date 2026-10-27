#!/bin/sh

# اجرای migration ها (اختیاری)
php artisan migrate --force

# اجرای PHP-FPM در پس‌زمینه
php-fpm -D

# اجرای Nginx در پیش‌زمینه (تا کانتینر زنده بماند)
nginx -g 'daemon off;'