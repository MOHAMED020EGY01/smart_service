FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
/etc/apache2/sites-available/*.conf

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

# 🔥 سكربت تشغيل يحل المشكلة وقت runtime
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
