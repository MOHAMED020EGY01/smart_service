FROM php:8.2-apache

# تثبيت الإضافات المطلوبة (مثال)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# إصلاح مشكلة MPM: تعطيل event وتفعيل prefork
RUN a2dismod mpm_event && a2enmod mpm_prefork

# تفعيل وحدة الـ PHP لـ Apache
RUN a2enmod php8.2

# أمر التشغيل (تأكد من أنه يطابق إعداداتك)
CMD ["apache2-foreground"]
