FROM richarvey/nginx-php-fpm:latest
COPY . /var/www/html
WORKDIR /var/www/html

# تنظیمات محیطی برای لاراول
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# اجازه دادن به Composer برای اجرا با کاربر root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]