FROM php:8.2-apache
COPY . /var/www/html/
EXPOSE 80
CMD php -S 0.0.0.0:$PORT -t /var/www/html/
