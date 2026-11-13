FROM php:8.2-apache

# Extensiones para conectar PHP con MySQL (PDO)
RUN docker-php-ext-install pdo pdo_mysql

# Código de la aplicación dentro de la imagen
COPY src/ /var/www/html/

EXPOSE 80
